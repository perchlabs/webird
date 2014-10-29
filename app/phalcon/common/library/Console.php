<?php
namespace Webird;

use Phalcon\CLI\Console as PhalconConsole,
    GetOptionKit\OptionCollection,
    GetOptionKit\OptionParser,
    GetOptionKit\Exception\RequireValueException,
    GetOptionKit\Exception\InvalidOptionException;

/**
 * Console class for all CLI applications
 */
class Console extends PhalconConsole
{
    private $argv;
    private $progPath;
    private $cmd;

    /**
     * Constructor
     *
     * @param \Phalcon\DI   $di
     */
    public function __construct($di, $defaultCmd, $argv)
    {
        parent::__construct($di);

        $this->progPath = $argv[0];

        if (isset($argv[1])) {
            $this->cmd = $argv[1];
            $this->argv = array_slice($argv, 1);
        } else {
            $this->cmd = $defaultCmd;
            $this->argv = [$defaultCmd];
        }
    }

    /**
     * handles the Console application and starts the Phalcon Module
     *
     * @param array  $arguments
     */
    public function handle($arguments)
    {
        global $argv;
        $config = $this->getDI()->get('config');

        if (in_array($this->cmd, ['list', 'help', '--help', '-h'])) {
            $this->printCmdList();
            exit(0);
        }

        if (strpos($this->cmd, '.') !== false || strpos($this->cmd, '/') !== false) {
            throw new \Exception('Invalid command name', 1);
        }

        // All environments
        if (file_exists("{$config->path->modulesDir}/cli/cmd/{$this->cmd}.php")) {
            $cmdDef = require("{$config->path->modulesDir}/cli/cmd/{$this->cmd}.php");
        }

        if (ENVIRONMENT == 'dev') {
            if (file_exists("{$config->dev->path->devDir}/cmd_overrides/{$this->cmd}.php")) {
                $cmdDef = require("{$config->dev->path->devDir}/cmd_overrides/{$this->cmd}.php");
            }
        }

        if (!isset($cmdDef)) {
            throw new \Exception('The command definition does not exist', 1);
        }

        list($task, $action) = explode('::', $cmdDef[0]);
        try {
            $taskParams = $this->buildTaskParams($this->argv, $cmdDef[1]);
            parent::handle([
                'module' => $arguments['module'],
                'task'   => 'Webird\Cli\Tasks\\' . ucfirst($task),
                'action' => $action,
                'params' => $taskParams
            ]);
        } catch (ArgumentValidationException $e) {
            printHelpRecommend($e->getMessage());
            exit(1);
        }
    }








    private function printCmdList()
    {
        $config = $this->getDI()->get('config');

        $appCmdDefs = $this->getCmdDefs($config->path->modulesDir . '/cli/cmd');
        if (ENVIRONMENT == 'dev') {
            $devCmdDefs = $this->getCmdDefs($config->dev->path->devDir . '/cmd_overrides');
            $appCmdDefs = array_replace($appCmdDefs, $devCmdDefs);
        }

        $cmdList = ['list' => "List available commands for current '" . ENVIRONMENT . "' environment"];
        foreach ($appCmdDefs as $cmdName => $cmdDef) {
            $cmdList[$cmdName] = $cmdDef[1]['title'];
        }

        ksort($cmdList);
        foreach ($cmdList as $cmdName => $title) {
            echo str_pad($cmdName, 15) . " : " . $title . "\n";
        }
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
     * Configures the parameters to be sent to each Task action
     *
     *  option configuration
     *  --------------------------------------------------------
     *  o|option         flag option (with boolean value true)
     *  option:          option requires a value
     *  option+          option with multiple values
     *  option?          option with optional values
     *  option:=string   option with type constraint of string
     *  option:=number   option with type constraint of number
     *  option:=file     option with type constraint of file
     *  option:=date     option with type constraint of date
     *  option:=boolean  option with type constraint of boolean
     *  o                single character only option
     *  option           long option name
     *
     * @param  array  $cmdDef
     * @return array
     */
    private function buildTaskParams($argv, $cmdDef)
    {
        // Configure the command line definition
        $specs = new OptionCollection();
        foreach ($cmdDef['opts'] as $option => $help) {
            $specs->add($option, $help);
        }
        // Every program will have an auto-generated help
        $specs->add('h|help', 'display this help and exit');

        // Assign the command definition
        try {
            $parser = new OptionParser($specs);
        } catch (\Exception $e) {
            error_log("$cmd: The program has misconfigured options.");
            exit(1);
        }

        // Use the options definition to parse the program arguments
        try {
            $result = $parser->parse($argv);
        } catch (RequireValueException $e) {
            $this->printHelpRecommend($e->getMessage());
            exit(1);
        } catch (InvalidOptionException $e) {
            $this->printHelpRecommend($e->getMessage());
            exit(1);
        } catch (\Exception $e) {
            $this->printHelpRecommend($e->getMessage());
            exit(1);
        }

        // Print the help if that option was selected
        if ($result->has('help')) {
            $this->printHelp($cmdDef, $specs);
            exit(0);
        }
        // Ensure that the required arguments are supplied
        if (count($result->arguments) - 1 < count($cmdDef['args']['required'])) {
            $this->printHelpRecommend('missing operand');
            exit(1);
        }

        // Clean arguments
        $args = array_map(function($arg) { return $arg->arg; }, $result->arguments);
        $args = array_splice($args, 1);
        // Clean options
        $opts = array_map(function($opt) { return $opt->value; }, $result->keys);
        // The final result to be used in Tasks
        $params = [
            'args' => $args,
            'opts' => $opts
        ];

        return $params;
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
