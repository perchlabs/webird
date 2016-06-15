<?php
namespace Webird\CLI;

use Phalcon\CLI\Console as PhalconConsole,
    Webird\CLI\Exception\ArgumentValidationException,
    Webird\CLI\Exception\PrintHelpException;

/**
 * Console class for all CLI applications
 */
class Console extends PhalconConsole
{
    /**
     *
     */
    private $progPath;

    /**
     *
     */
    private $cmd;

    /**
     * Constructor
     *
     * @param \Phalcon\DI   $di
     */
    public function __construct($di)
    {
        parent::__construct($di);
    }

    /**
     * handles the Console application and starts the Phalcon Module
     *
     * @param array  $arguments
     */
    public function handle($arguments = null)
    {
        $config = $this->getDI()->getConfig();

        $this->progPath = $arguments['params'][0];
        if (isset($arguments['params'][1])) {
            $this->cmd = $arguments['params'][1];
        } else {
            if (!isset($arguments['defaultCmd'])) {
                throw new \Exception('The Console was not given a command', 1);
            }
            $this->cmd = $arguments['defaultCmd'];
        }

        $params = array_merge(
            [$arguments['params'][0]],
            array_slice($arguments['params'], 2)
        );

        if (in_array($this->cmd, ['help', '--help', '-h'])) {
            $this->printCmdList();
            exit(0);
        }

        if (strpos($this->cmd, '.') !== false || strpos($this->cmd, '/') !== false) {
            throw new \Exception('Invalid command name', 1);
        }

        // All environments
        $cmdArr = require("{$config->path->modulesDir}/{$arguments['module']}/cmd.php");

        if (DEVELOPING) {
            $devCmdArr = require("{$config->dev->path->devDir}/cmd_overrides.php");
            $cmdArr = array_replace($cmdArr, $devCmdArr);
        }

        if (!array_key_exists($this->cmd, $cmdArr)) {
            throw new \Exception("The command '{$this->cmd}' does not exist", 1);
        }

        $taskParts = explode('::', $cmdArr[$this->cmd]);
        $task = $taskParts[0];
        $action = (isset($taskParts[1])) ? $taskParts[1] : 'main';
        try {
            parent::handle([
                'module' => $arguments['module'],
                'task'   => 'Webird\Modules\Cli\Tasks\\' . ucfirst($task),
                'action' => $action,
                'params' => $params
            ]);
        } catch (ArgumentValidationException $e) {
            $this->printHelpRecommend($e->getMessage());
            exit(1);
        } catch (PrintHelpException $e) {
            if ($e->getCode() == 1) {
                $this->printHelpRecommend($e->getMessage());
                exit(1);
            } else {
                $this->printHelp($e->getCmdDef(), $e->getSpecs());
                exit(0);
            }
        }
    }

    /**
     *
     */
    private function printCmdList()
    {
        $config = $this->getDI()->get('config');

        // All environments
        $cmdArr = require("{$config->path->modulesDir}/cli/cmd.php");

        if (DEVELOPING) {
            $devCmdArr = require("{$config->dev->path->devDir}/cmd_overrides.php");
            $cmdArr = array_replace($cmdArr, $devCmdArr);
        }

        $cmdList = array_keys($cmdArr);
        sort($cmdList);

        echo "Available commands:\n";
        echo implode(', ', $cmdList) . "\n";
    }

    /**
     * Print an error message message with a recommendation to access help
     *
     * @param string  $message
     */
    private function printHelpRecommend($message)
    {
        error_log("{$this->progPath} {$this->cmd}: $message");
        error_log("Try {$this->progPath} {$this->cmd} --help' for more information.");
    }

    /**
     * Prints a the program help
     *
     * @param array                           $cmdDef
     * @param \GetOptionKit\OptionCollection  $specs
     */
    private function printHelp($cmdDef, $specs)
    {
        $reqArgs = array_map('strtoupper', $cmdDef['args']['required']);
        $optArgs = array_map(function($arg) {
            return '[' . strtoupper($arg) . ']';
        }, $cmdDef['args']['optional']);

        $args = array_merge($reqArgs, $optArgs);
        $argNames = implode(' ', $args);

        echo "Usage: {$this->progPath} {$this->cmd} [OPTION] $argNames\n";
        echo "{$cmdDef['title']}\n";
        if (isset($cmdDef['help'])) {
            echo "{$cmdDef['help']}\n";
        }
        echo "\n";

        $widths = array_map(function($spec) {
            return strlen($spec->renderReadableSpec());
        }, $specs->all());
        $width = max($widths);

        $lines = [];
        foreach($specs->all() as $spec)
        {
            $c1 = str_pad($spec->renderReadableSpec(), $width);
            $line = sprintf("%s  %s", $c1, $spec->desc);
            $lines[] = $line;
        }
        foreach ($lines as $line) {
            $line = trim($line);
            echo " $line\n";
        }
    }

}
