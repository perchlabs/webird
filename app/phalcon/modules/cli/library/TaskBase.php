<?php
namespace Webird\Cli;

use Phalcon\CLI\Task As PhalconTask,
    Webird\Cli\Exception\ArgumentValidationException;

// TODO, FIXME: Review if webUser, webGroup variables are still needed

/**
 * Task Base for \Webird\Console applications
 *
 */
abstract class TaskBase extends PhalconTask
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
     * Ensures that the required number of CLI arguments are included
     *
     * @param  array   $params
     * @param  int     $requiredArgCount
     */
    protected function ensureArgumentCount($params, $requiredArgCount)
    {
        if (count($params['args']) < $requiredArgCount) {
            throw new \Exception("Invalid number of requirement arguments.", 1);
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
        // echo "$cmd\n";
        exec($cmd, $output, $ret);
        if ($ret !== 0) {
            error_log('Unable to determine the groups this user belongs to.');
            exit(1);
        }
        // echo $output[0] . "\n";

        $parts = explode(':', $output[0]);
        $parts = trim($parts[1]);
        $groupArr = explode(' ', $parts);

        $groupFound = in_array($groupName, $groupArr, true);

        return $groupFound;
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
