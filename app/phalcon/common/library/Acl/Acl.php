<?php
namespace Webird\Acl;

use Phalcon\Mvc\User\Component,
    Phalcon\Acl\Role as AclRole,
    Phalcon\Acl\Resource as AclResource,
    Webird\Models\Roles,
    Webird\Acl\Adapter\Memory as AclMemory;

/**
 * Webird\Acl\Acl
 */
class Acl extends Component
{

    /**
     * The ACL object
     *
     * @var \Webird\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * Define all of the resources for controller => actions.
     *
     * @var array
     */
    private $specPrivate;

    /**
     * Define the resources that are considered "public". These controller => actions require no authentication.
     *
     * @var array
     */
    private $specPublic;

    /**
     * Human-readable descriptions of the actions used in {@see $privateResources}
     *
     * @var array
     */
    private $actionDescriptions;

    /**
     * Class Constructor
     * Builds the map of private resources by subtracting the public resources from the
     * entire list of resources
     *
     */
    public function __construct($aclData)
    {
        $this->specPrivate = $this->parseRawResources($aclData['private']);

        $this->specPublic = $this->parseRawResources($aclData['public']);
        foreach ($this->specPublic as $namespace => $resources) {
            $this->specPublic[$namespace]['errors'] = ['*'];
        }

        $this->actionDescriptions = [];
    }

    /**
     * Checks if a resource is public
     *
     * @param string $namespace
     * @param string $resource
     * @param string $action
     * @return boolean
     */
    public function isPublic($namespace, $resource, $action)
    {
        try {
            return $this->getAcl()->isPublic($namespace, $resource, $action);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
    }

    /**
     * Checks if the current role is allowed to access a resource
     *
     * @return boolean
     */
    public function isAllowed(...$argv)
    {
        $argc = count($argv);
        if ($argc === 3) {
            list($role, $nsRes, $action) = $argv;
            list($namespace, $resource) = explode('::', $nsRes);
        } elseif ($argc === 4) {
            list($role, $namespace, $resource, $action) = $argv;
            $nsRes = $this->mergeResource($namespace, $resource);
        } else {
            throw new \Exception('Invalid number of arguments');
        }

        if ($this->getAcl()->isPublic($namespace, $resource, $action)) {
            return true;
        } elseif (!$this->getAcl()->isRole($role)) {
            return false;
        } else if (!$this->getAcl()->isResource($nsRes)) {
            return false;
        }

        return $this->getAcl()->isAllowed($role, $nsRes, $action);
    }

    /**
     * Returns the permissions assigned to a role
     *
     */
     public function getPrivateSpec()
     {
        return $this->specPrivate;
     }

    /**
     * Returns the permissions assigned to a role
     *
     * @param Roles $roles
     * @return array
     */
    public function getPermissions(Roles $role)
    {
        $permissions = [];
        foreach ($role->getPermissions() as $permission) {
            $permissions[$permission->namespace . '::' . $permission->resource . '::' . $permission->action] = true;
        }
        return $permissions;
    }

    /**
     * Returns all the resources and their actions available in the application
     *
     * @return array
     */
    public function getResources()
    {
        return $this->getAcl()->getResources();
    }

    public function isAction($module, $controller, $action)
    {
        return $this->getAcl()->isAction($module, $controller, $action);
    }

    /**
     * Returns the action description according to its simplified name
     *
     * @param string $action
     * @return $action
     */
    public function getActionDescription($action)
    {
        return (isset($this->actionDescriptions[$action])) ? $this->actionDescriptions[$action] : $action;
    }

    /**
     * Returns the ACL list
     *
     * @return Phalcon\Acl\Adapter\Memory
     */
    protected function getAcl()
    {
        // Check if the ACL is already created
        if (is_object($this->acl)) {
            return $this->acl;
        }

        $this->acl = $this->buildAcl();
        return $this->acl;
    }

    /**
     *
     */
    private function buildAcl()
    {
        return new AclMemory($this->specPrivate, $this->specPublic);
    }

    /**
     *
     */
    protected function mergeResource($namespace, $resource)
    {
        return ($namespace == '') ? $resource : "{$namespace}::{$resource}";
    }

    /**
     *
     */
    protected function parseRawResources($rawResources)
    {
        return $rawResources;
    }

}
