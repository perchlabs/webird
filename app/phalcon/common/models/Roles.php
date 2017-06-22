<?php
namespace Webird\Models;

use Webird\Mvc\Model;
use Webird\Models\Users;
use Webird\Models\Permissions;

/**
 * Webird\Models\Roles
 * All the role levels in the application. Used in conjenction with ACL lists
 */
class Roles extends Model
{

    /**
     * ID
     * @var integer
     */
    public $id;

    /**
     * Name
     * @var string
     */
    public $name;

    /**
     * Define relationships to Users and Permissions
     */
    public function initialize()
    {
        $this->hasMany('id', Users::class, 'rolesId', [
            'alias' => 'users',
            'foreignKey' => [
                'message' => 'Role cannot be deleted because it\'s used on Users',
            ],
        ]);

        $this->hasMany('id', Permissions::class, 'rolesId', [
            'alias' => 'permissions',
        ]);
    }
}
