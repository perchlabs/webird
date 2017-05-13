<?php
namespace Webird\Cli;

use Phalcon\CLI\Task As PhalconTask;
use GetOptionKit\OptionCollection;
use GetOptionKit\OptionParser;
use GetOptionKit\Exception\RequireValueException;
use GetOptionKit\Exception\InvalidOptionException;
use Webird\CLI\Exception\ArgumentValidationException;
use Webird\CLI\Exception\PrintHelpException;

// TODO, FIXME: Review if webUser, webGroup variables are still needed

/**
 * Task Base for \Webird\Console applications
 *
 */
abstract class Task extends PhalconTask
{
    /**
     * Converts command like 'yes', 'y', 'true', 'no', 'n' and 'false' to boolean
     *
     * @param  array   $opts
     * @param  string  $name
     * @return string
     */
    protected function castStringToBoolean($opts, $name)
    {
        $opt = $opts[$name];

        $opt = strtolower($opt);
        if ($opt == 'y' || $opt == 'yes' || $opt == 'true') {
            return true;
        } else if ($opt == 'n' || $opt == 'no' || $opt == 'false') {
            return false;
        } else {
            throw new ArgumentValidationException("Option $name must be boolean", 1);
        }
    }

    /**
     * Get the system user of the user of the current process
     *
     * @return string
     */
    protected function getProcessUser()
    {
        // $userInfo = posix_getpwuid(posix_geteuid());
        $userInfo = posix_getpwuid(posix_getuid());
        $userName = $userInfo['name'];
        return $userName;
    }

    /**
     * Get the system group of the user of the current process
     *
     * @return string
     */
    protected function getProcessGroup()
    {
        // $groupInfo = posix_getgrgid(posix_getegid());
        $groupInfo = posix_getgrgid(posix_getgid());
        $groupName = $groupInfo['name'];
        return $groupName;
    }

    /**
     * Checks the Unix system groups to see that the user is in the group
     *
     * @param  string  $userName
     * @param  string  $groupName
     */
    protected function isSystemUserInGroup($userName, $groupName)
    {
        $userNameEsc = escapeshellarg($userName);
        $cmd = "groups $userNameEsc";
        exec($cmd, $output, $ret);
        if ($ret !== 0) {
            error_log('Unable to determine the groups this user belongs to.');
            exit(1);
        }

        $parts = explode(':', $output[0]);
        $parts = trim($parts[1]);
        $groupArr = explode(' ', $parts);

        $groupFound = in_array($groupName, $groupArr, true);

        return $groupFound;
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
    protected function parseArgs($argv, $cmdDef)
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

        if (isset($argv[1]) && in_array($argv[1], ['--help', '-h'])) {
            throw new PrintHelpException($cmdDef, $specs);
        }

        // Use the options definition to parse the program arguments
        try {
            $result = $parser->parse($argv);
        } catch (RequireValueException $e) {
            throw new PrintHelpException($cmdDef, $specs, $e->getMessage(), 1);
        } catch (InvalidOptionException $e) {
            throw new PrintHelpException($cmdDef, $specs, $e->getMessage(), 1);
        } catch (\Exception $e) {
            throw new PrintHelpException($cmdDef, $specs, $e->getMessage(), 1);
        }

        // Ensure that the required arguments are supplied
        if (count($result->arguments) < count($cmdDef['args']['required'])) {
            throw new PrintHelpException($cmdDef, $specs, 'missing operand', 1);
        }

        // Clean arguments
        $args = array_map(function($arg) { return $arg->arg; }, $result->arguments);
        // Clean options
        $opts = array_map(function($opt) { return $opt->value; }, $result->keys);

        // The final result to be used in Tasks
        return [
            'args' => $args,
            'opts' => $opts,
        ];
    }

    // /**
    //  * Checks if the system web user can read the current users files
    //  *
    //  * @return boolean
    //  */
    // protected function canWebUserReadWriteProcessUserFiles()
    // {
    //     $webUser = $this->config->app->webUser;
    //     $webGroup = $this->config->app->webGroup;
    //     $processUser = $this->getProcessUser();
    //     $processGroup = $this->getProcessGroup();
    //
    //     if ($webUser === $processUser) {
    //         // echo "TRUE:1\n";
    //         return true;
    //     }
    //     if ($this->isSystemUserInGroup($webUser, $processGroup)) {
    //         // echo "TRUE:2\n";
    //         return true;
    //     }
    //
    //     return false;
    // }

    // /**
    //  * Asert that system web user can read the current users files
    //  *
    //  */
    // protected function ensureWebUserCanReadFiles()
    // {
    //     // First check if the current user is compatible with the web user
    //     if ($this->canWebUserReadWriteProcessUserFiles()) {
    //         return true;
    //     }
    //
    //     // Next try to change the current user to the web user.  This is only possible when running as root.
    //     try {
    //         $this->changeProcessToWebUser();
    //         if (! $this->canWebUserReadWriteProcessUserFiles()) {
    //             throw new \Exeption("Error: The user and group id change attempt was unsuccessful.");
    //         }
    //         return true;
    //     } catch (\Exception $ex) {
    //         error_log($ex->getMessage());
    //     }
    //
    //     error_log("Error: The web user will not be able to read and write the files.");
    //     error_log("You may try one of the following techniques:");
    //     error_log("1) Try adding the web user to your personal group and restarting the web server");
    //     error_log("2) Try running the script as root.");
    //     exit(1);
    // }

    /**
     * Check that the current user is the web user
     *
     * @return boolean
     */
    protected function ensureRunningAsWebUser()
    {
        // Next try to change the current user to the web user.  This is only possible when running as root.
        try {
            $this->changeProcessToWebUser();
            // if (! $this->canWebUserReadWriteProcessUserFiles()) {
            //     throw new \Exception("Error: The user and group id change attempt was unsuccessful.");
            // }
            return true;
        } catch (\Exception $ex) {
            error_log($ex->getMessage());
            error_log("This process must be run as the web user or as a user (root) capable of changing to the web user.");
        }

        exit(1);
    }

    /**
     * Changes the current user to the web user
     *
     * @return boolean
     */
    private function changeProcessToWebUser()
    {
        $userInfo = posix_getpwnam($this->config->app->webUser);
        $uidWeb = $userInfo['uid'];
        $gidWeb = $userInfo['gid'];

        // If the current user is the web user then success
        $uid = posix_getuid();
        $gid = posix_getgid();
        if ($uid === $uidWeb && $gid === $gidWeb) {
            return true;
        }

        // If the user is not root then there is no chance to succeed.
        if ($uid !== 0) {
            throw new \Exception("Error: The process real group id could not be changed.");
        }
        // If the user is root then attempt to change to the web user and group
        if (posix_setgid($gidWeb) !== true) {
            throw new \Exception("Error: The process real group id could not be changed.");
        }
        if (posix_setuid($uidWeb) !== true) {
            throw new \Exception("Error: The process real user id could not be changed.");
        }

        $uid = posix_getuid();
        $gid = posix_getgid();
        if ($uid !== $uidWeb || $gid !== $gidWeb) {
            throw new \Exeption("Error: Somehow the user was not changed to the web user.");
        }

        return true;
    }

}
