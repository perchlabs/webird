<?php
namespace Webird\Acl\Adapter;

use Phalcon\Acl;
use Phalcon\Acl\Adapter\Memory as AclMemory;
use Phalcon\Acl\Role as AclRole;
use Phalcon\Acl\Resource as AclResource;
use Webird\Models\Roles;

/**
 * Webird\Acl\Adapter\Memory
 */
class Memory extends AclMemory
{
    /**
     *
     */
    private $resourceActionList;

    /**
     *
     */
    private $publicRoleName;

    /**
     *
     */
    public function getPublicRoleName()
    {
        return $this->publicRoleName;
    }

    /**
     * Class Constructor
     * Builds the map of private resources by subtracting the public resources from the
     * entire list of resources
     *
     */
    public function __construct($specPrivate, $specPublic)
    {
        parent::__construct();

        $this->publicRoleName = '__public__';

        $this->setDefaultAction(Acl::DENY);

        $this->addSpec($specPrivate);
        $this->addSpec($specPublic);

        $this->addPrivateRoles($specPrivate);
        $this->addPublicRoles($specPublic);
    }

    /**
     *
     */
    public function isPublic($namespace, $resource, $action)
    {
        $nsRes = $this->mergeResource($namespace, $resource);
        if (!$this->isResource($nsRes)) {
            return false;
        }

        return $this->isAllowed($this->getPublicRoleName(), $nsRes, $action);
    }

    /**
     *
     */
    public function isAction($namespace, $resource, $action)
    {
        return isset($this->resourceActionList["{$namespace}::{$resource}::{$action}"]);
    }

    /**
     *
     */
    public function allow($roleName, $nsRes, $actionParam, $func = null)
    {
        if (is_array($actionParam)) {
            foreach ($actionParam as $action) {
                $this->resourceActionList["{$nsRes}::{$action}"] = true;
            }
        } else {
            $this->resourceActionList["{$nsRes}::{$actionParam}"] = true;
        }

        parent::allow($roleName, $nsRes, $actionParam);
    }

    /**
     *
     */
    protected function addSpec($spec)
    {
        foreach ($spec as $namespace => $resources) {
            foreach ($resources as $resource => $actions) {
                $this->addResource(new AclResource($this->mergeResource($namespace, $resource)), $actions);
            }
        }
    }

    /**
     *
     */
    protected function addPrivateRoles($spec)
    {
        // Register roles
        $roles = Roles::find([
            'active = :active:',
            'bind' => [
                'active' => 'Y'
            ]
        ]);

        foreach ($roles as $role) {
            $this->addRole(new AclRole($role->name));
        }

        // Grant access to private area
        foreach ($roles as $role) {

            // Grant permissions in "permissions" model
            foreach ($role->getPermissions() as $permission) {
                $this->allow($role->name, $permission->getNamespaceResource(), $permission->action);
            }
        }
    }

    /**
     *
     */
    protected function addPublicRoles($spec)
    {
        $roleName = $this->getPublicRoleName();
        $this->addRole($roleName);
        foreach ($spec as $namespace => $resources) {
            foreach ($resources as $resource => $actions) {
                $this->allow($roleName, $this->mergeResource($namespace, $resource), $actions);
            }
        }
    }

    /**
     *
     */
    protected function mergeResource($namespace, $resource)
    {
        return ($namespace == '') ? $resource : "{$namespace}::{$resource}";
    }

}
