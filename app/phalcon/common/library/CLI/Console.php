<?php
namespace Webird\CLI;

use Phalcon\CLI\Console as PhalconConsole;

/**
 * Console class for all CLI applications
 */
class Console extends PhalconConsole
{
    private $progPath;


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

        $this->progPath = array_shift($arguments['params']);

        if (isset($arguments['params'][0])) {
            $cmd = $arguments['params'][0];
        } else {
            if (!isset($arguments['defaultCmd'])) {
                throw new \Exception('The Console was not given a command', 1);
            }
            $cmd = $arguments['defaultCmd'];
        }
        $cmd = (isset($arguments['params'][0])) ? $arguments['params'][0] : $arguments['defaultCmd'];

        if (in_array($cmd, ['help', '--help', '-h'])) {
            $this->printCmdList();
            exit(0);
        }

        if (strpos($cmd, '.') !== false || strpos($cmd, '/') !== false) {
            throw new \Exception('Invalid command name', 1);
        }

        // All environments
        $cmdArr = require("{$config->path->modulesDir}/cli/cmd.php");

        if (DEV_ENV === ENV) {
            $devCmdArr = require("{$config->dev->path->devDir}/cmd_overrides.php");
            $cmdArr = array_replace($cmdArr, $devCmdArr);
        }

        if (!array_key_exists($cmd, $cmdArr)) {
            throw new \Exception('The command description does not exist', 1);
        }

        $taskParts = explode('::', $cmdArr[$cmd]);
        $task = $taskParts[0];
        $action = (isset($taskParts[1])) ? $taskParts[1] : 'main';
        try {
            parent::handle([
                'module' => $arguments['module'],
                'task'   => 'Webird\Cli\Tasks\\' . ucfirst($task),
                'action' => $action,
                'params' => $arguments['params']
            ]);
        } catch (ArgumentValidationException $e) {
            printHelpRecommend($e->getMessage());
            exit(1);
        } catch (ArgumentPrintHelpException $e) {
            if ($e->getCode() == 1) {
                $this->printHelpRecommend($e->getMessage());
            } else {
                $this->printHelp($e->getCmdDef(), $e->getSpecs());
            }
            exit(1);
        }
    }






    private function printCmdList()
    {
        $config = $this->getDI()->get('config');

        // All environments
        $cmdArr = require("{$config->path->modulesDir}/cli/cmd.php");

        if (DEV_ENV === ENV) {
            $devCmdArr = require("{$config->dev->path->devDir}/cmd_overrides.php");
            $cmdArr = array_replace($cmdArr, $devCmdArr);
        }

        $cmdList = array_keys($cmdArr);
        sort($cmdList);

        echo "Available commands:\n";
        echo implode(', ', $cmdList) . "\n";
    }







    private function getCmdDefs($path)
    {
        $config = $this->getDI()->get('config');

        $cmdArr = [];
        $dh = opendir($path);
        while (($fileName = readdir($dh)) !== false) {
            if ($fileName == '.' || $fileName == '..')
                continue;

            $fullPath = realpath("$path/$fileName");
            $cmdName = str_replace('.php', '', $fileName);
            $cmdArr[$cmdName] = require($fullPath);
        }
        closedir($dh);

        return $cmdArr;
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
