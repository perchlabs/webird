<?php
namespace Webird\Cli\Tasks;

use Webird\Models\Users,
    Webird\Models\Roles,
    Webird\Models\PasswordChanges,
    Webird\Models\EmailConfirmations,
    Webird\Cli\TaskBase,
    Webird\Cli\Exception\ArgumentValidationException;

/**
 * Task for user functions
 *
 */
class UserTask extends TaskBase
{
    public function mainAction() {
         echo "The default action inside of the ", CURRENT_TASK, " task is not configured\n";
    }



    public function createAction(array $params)
    {
        $this->ensureArgumentCount($params, 2);
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

        $user = new Users();
        $user->assign([
            'name' => $name,
            'rolesId' => $role->id,
            'email' => $email,
            'active' => $active,
            'password' => $password
        ]);

        if (!$user->save()) {
            $message = implode("\n", $user->getMessages());
            throw new \Exception("$message", 1);
        } else {
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




    public function deleteAction(array $params)
    {
        $this->ensureArgumentCount($params, 1);
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







    public function statusAction(array $params)
    {
        $this->ensureArgumentCount($params, 1);
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






    public function passwordAction(array $params)
    {
        $this->ensureArgumentCount($params, 2);
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








    private function getRoleByUniqueRef($roleRef)
    {
        // Allow the role to be specified as a primary key or by the exact name
        if (ctype_digit($roleRef)) {
            $roleParam = [
                'conditions' => 'id = ?0 AND active = ?1',
                'bind' => [$roleRef, 'Y']
            ];
        } else {
            $roleParam = [
                'conditions' => 'name = ?0 AND active = ?1',
                'bind' => [$roleRef, 'Y']
            ];
        }
        if (($role = Roles::findFirst($roleParam)) === false) {
            throw new ArgumentValidationException("Unable to find role $roleRef", 1);
        }

        return $role;
    }


}
