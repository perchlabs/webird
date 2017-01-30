<?php
namespace Webird\Modules\Cli\Tasks;

use Webird\Models\Users;
use Webird\Models\Roles;
use Webird\Models\PasswordChanges;
use Webird\Models\EmailConfirmations;
use Webird\CLI\Task;
use Webird\CLI\Exception\ArgumentValidationException;

/**
 * Task for user functions
 *
 */
class UserTask extends Task
{
    /**
     *
     */
    public function mainAction() {
         echo "The default action inside of the ", CURRENT_TASK, " task is not configured\n";
    }

    /**
     *
     */
    public function createAction($argv)
    {
        $params = $this->parseArgs($argv, [
            'title' => 'Add a user with a permission role.',
            'args' => [
                'required' => ['email', 'role'],
                'optional' => [],
            ],
            'opts' => [
                'p|password:' => 'set user password (otherwise it will need to be on first login).',
                'a|activate' => 'activate',
                'E|send-email?' => 'send email confirmation with optional message',
            ],
        ]);
        list($emailRaw, $roleRef) = $params['args'];
        $opts = $params['opts'];

        $emailParts = mailparse_rfc822_parse_addresses($emailRaw);
        if (empty($emailParts) || $emailParts[0]['display'] == $emailParts[0]['address']) {
            throw new ArgumentValidationException('Email must be in form: display <address>', 1);
        }

        $name = $emailParts[0]['display'];
        $email = $emailParts[0]['address'];

        // Validate the email
        if (($email = filter_var($email, FILTER_VALIDATE_EMAIL)) === false) {
            throw new ArgumentValidationException('Email is invalid', 1);
        }

        $role = $this->getRoleByUniqueRef($roleRef);

        // Validate the password
        if (isset($opts['password'])) {
            $password = $opts['password'];
            $passwordMinLength = $this->config->security->passwordMinLength;
            if (mb_strlen($password) < $passwordMinLength) {
                throw new ArgumentValidationException("Password must be at least $passwordMinLength characters", 1);
            }
        }
        else {
            // The model will check for an empty string and will create a random password
            $password = '';
        }

        // Check for CLI flags
        $active = (isset($opts['activate'])) ? 'Y' : 'N';
        $sendEmail = (array_key_exists('send-email', $opts));
        $emailExtraMsg = (isset($opts['send-email'])) ? trim($opts['send-email']) : '';

        $user = new Users([
            'name' => $name,
            'rolesId' => $role->id,
            'email' => $email,
            'active' => $active,
            'password' => $password,
        ]);

        if (!$user->save()) {
            $message = implode("\n", $user->getMessages());
            throw new \Exception("$message", 1);
        }

        if ($sendEmail) {
            echo "Sending email confirmation to user\n";
            $emailConfirmation = new EmailConfirmations();
            $emailConfirmation->usersId = $user->id;
            $emailConfirmation->extraMsg = $emailExtraMsg;
            if (!$emailConfirmation->save()) {
                $message = implode("\n", $emailConfirmation->getMessages());
                throw new \Exception($message, 1);
            }
        }
    }

    /**
     *
     */
    public function deleteAction($argv)
    {
        $params = $this->parseArgs($argv, [
            'title' => 'Delete a user by email or primary key.',
            'args' => [
                'required' => ['user'],
                'optional' => [],
            ],
            'opts' => [],
        ]);
        list($userRef) = $params['args'];

        if (($user = $this->getUserByUniqueRef($userRef)) === false) {
            throw new \Exception("Unable to locate user $userRef", 1);
        }

        if (!$user->delete()) {
            $message = implode("\n", $user->getMessages());
            throw new \Exception("$message", 1);
        } else {
        }
    }

    /**
     *
     */
    public function statusAction($argv)
    {
        $params = $this->parseArgs($argv, [
            'title' => 'Modify or view status for a user by email or primary key.',
            'args' => [
                'required' => ['user'],
                'optional' => [],
            ],
            'opts' => [
                'r|role'                  => 'Set user permission role',
                'a|active:'               => 'Set user active status',
                'b|banned:'               => 'Set user banned status. A banned user is also deactivated',
                'm|must-change-password:' => 'Set must change password status',
            ],
        ]);

        list($userRef) = $params['args'];
        $opts = $params['opts'];

        if (($user = $this->getUserByUniqueRef($userRef)) === false) {
            throw new \Exception("Unable to locate user $userRef", 1);
        }

        // If there is nothing to be done then print the user status
        if (empty($opts)) {
            $role = $this->getRoleByUniqueRef($user->rolesId);
            echo "id             : {$user->id}\n";
            echo "email          : {$user->email}\n";
            echo "name           : {$user->name}\n";
            echo "user role      : {$role->id}, {$role->name}\n";
            echo "must change pw : {$user->mustChangePassword}\n";
            echo "active         : {$user->active}\n";
            echo "banned         : {$user->banned}\n";
            exit(0);
        }

        if (isset($opts['role'])) {
            $role = $this->getRoleByUniqueRef($opts['role']);
            $user->rolesId = $role->id;
        }

        if (isset($opts['active'])) {
            $active = $this->castStringToBoolean($opts, 'active');
            $user->active = ($active) ? 'Y' : 'N';
        }
        if (isset($opts['banned'])) {
            $banned = $this->castStringToBoolean($opts, 'banned');
            $user->banned = ($banned) ? 'Y' : 'N';
        }
        if (isset($opts['must-change-password'])) {
            $mustChangePassword = $this->castStringToBoolean($opts, 'must-change-password');
            $user->mustChangePassword = ($mustChangePassword) ? 'Y' : 'N';
        }

        if (!$user->save()) {
            $message = implode("\n", $user->getMessages());
            throw new \Exception("$message", 1);
        } else {
        }
    }

    /**
     *
     */
    public function passwordAction($argv)
    {
        $params = $this->parseArgs($argv, [
            'title' => 'Change the password of an existing user by email or primary key.',
            'args' => [
                'required' => ['user', 'new_password'],
                'optional' => [],
            ],
            'opts' => [],
        ]);
        list($userRef, $password) = $params['args'];

        $passwordMinLength = $this->config->security->passwordMinLength;

        if (mb_strlen($password) < $passwordMinLength) {
            throw new ArgumentValidationException("Password must be at least $passwordMinLength characters", 1);
        }

        if (($user = $this->getUserByUniqueRef($userRef)) === false) {
            throw new \Exception("Unable to locate user $userRef", 1);
        }

        $user->password = $password;

        if (!$user->save()) {
            $message = implode("\n", $user->getMessages());
            throw new \Exception("$message", 1);
        } else {
        }
    }

    /**
     *
     */
    private function getUserByUniqueRef($userRef)
    {
        if (ctype_digit($userRef)) {
            $user = Users::findFirstById($userRef);
        } else if (($email = filter_var($userRef, FILTER_VALIDATE_EMAIL)) !== false) {
            $user = Users::findFirstByEmail($email);
        }
        else {
            throw new ArgumentValidationException('The user must be specified as an email or primary key.', 1);
        }

        return $user;
    }

    /**
     *
     */
    private function getRoleByUniqueRef($roleRef)
    {
        // Allow the role to be specified as a primary key or by the exact name
        if (ctype_digit($roleRef)) {
            $roleParam = [
                'conditions' => 'id = ?0 AND active = ?1',
                'bind' => [$roleRef, 'Y'],
            ];
        } else {
            $roleParam = [
                'conditions' => 'name = ?0 AND active = ?1',
                'bind' => [$roleRef, 'Y'],
            ];
        }
        if (($role = Roles::findFirst($roleParam)) === false) {
            throw new ArgumentValidationException("Unable to find role $roleRef", 1);
        }

        return $role;
    }

}
