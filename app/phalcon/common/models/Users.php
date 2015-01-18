<?php
namespace Webird\Models;

use Phalcon\Mvc\Model\Validator\Uniqueness,
    Webird\Mvc\Model,
    Webird\Mvc\Model\Behavior\Blameable as BlameableBehavior;

/**
 * Webird\Models\Users
 * All the users registered in the application
 */
class Users extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var string
     */
    public $name;

    /**
     *
     * @var string
     */
    public $email;

    /**
     *
     * @var string
     */
    public $password;

    /**
     *
     * @var string
     */
    public $mustChangePassword;

    /**
     *
     * @var string
     */
    public $rolesId;

    /**
     *
     * @var string
     */
    public $banned;

    /**
     *
     * @var string
     */
    public $active;

    public function isActive()
    {
        return $this->active === 'Y';
    }

    public function isBanned()
    {
        return $this->banned !== 'N';
    }

    public function isDeleted()
    {
        return $this->deleted !== 'N';
    }

    /**
     * Before create the user assign a password
     */
    protected function beforeValidationOnCreate()
    {
        if (empty($this->password)) {

            // The user must change its password in first signin
            $this->mustChangePassword = 'Y';

            // Generate a plain temporary password
            $newPassword = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(12)));
        } else {
            // The user must not change its password in first signin
            $this->mustChangePassword = 'N';

            $newPassword = $this->password;
        }

        // Use this password as default
        $this->password = $this->getDI()
            ->getSecurity()
            ->hash($newPassword);

        // The account must be confirmed via e-mail unless specifically activated
        if ($this->active !== 'Y') {
            $this->active = 'N';
        }

        // The account is not banned by default
        $this->banned = 'N';

        $this->deleted = 'N';
    }

    /**
     * Before update the user
     */
    protected function beforeValidationOnUpdate()
    {
        if ($this->hasChanged('password')) {
            $this->password = $this->getDI()
                ->getSecurity()
                ->hash($this->password);
        }
    }

    /**
     * Before saving
     */
    protected function beforeSave()
    {
        // Ensure that a banned user cannot be accidentally let back in through a password
        // confirmation or another type of activation.
        if ($this->isBanned()) {
            $this->active = 'N';
        }
    }

    /**
     * Validate that emails are unique across users
     */
    protected function validation()
    {
        $this->validate(new Uniqueness([
            "field" => "email",
            "message" => "The email is already registered"
        ]));

        return $this->validationHasFailed() != true;
    }

    protected function initialize()
    {
        $this->keepSnapshots(true);

        $this->addSoftDeleteBehavior([
            'field'   => 'deleted',
            'value'   => 'Y',
            'cascade' => true
        ]);

        $this->belongsTo('rolesId', 'Webird\Models\Roles', 'id', [
            'alias' => 'role',
            'reusable' => true
        ]);

        $this->hasMany('id', 'Webird\Models\SuccessSignins', 'usersId', [
            'alias' => 'successSignins',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system'
            ]
        ]);

        $this->hasMany('id', 'Webird\Models\PasswordChanges', 'usersId', [
            'alias' => 'passwordChanges',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system'
            ]
        ]);

        $this->hasMany('id', 'Webird\Models\ResetPasswords', 'usersId', [
            'alias' => 'resetPasswords',
            'foreignKey' => [
                'message' => 'User cannot be deleted because he/she has activity in the system'
            ]
        ]);
    }
}
