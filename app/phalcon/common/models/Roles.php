<?php
namespace Webird\Models;

use Webird\Mvc\Model;

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
        $this->hasMany('id', 'Webird\Models\Users', 'rolesId', [
            'alias' => 'users',
            'foreignKey' => [
                'message' => 'Role cannot be deleted because it\'s used on Users'
            ]
        ]);

        $this->hasMany('id', 'Webird\Models\Permissions', 'rolesId', [
            'alias' => 'permissions'
        ]);
    }
}
