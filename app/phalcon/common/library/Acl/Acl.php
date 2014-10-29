<?php
namespace Webird\Acl;

use Phalcon\Mvc\User\Component,
    Phalcon\Acl\Adapter\Memory as AclMemory,
    Phalcon\Acl\Role as AclRole,
    Phalcon\Acl\Resource as AclResource,
    Webird\Models\Roles;

/**
 * Webird\Acl\Acl
 */
class Acl extends Component
{

    /**
     * The ACL Object
     *
     * @var \Phalcon\Acl\Adapter\Memory
     */
    private $acl;

    /**
     * Define all of the resources for controller => actions.
     *
     * @var array
     */
    private $specComplete;

    /**
     * Define the resources that are considered "public". These controller => actions require no authentication.
     *
     * @var array
     */
    private $specPublic;

    private $privateResources;

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
        $this->specComplete = $this->parseRawResources($aclData['complete']);
        $this->specPublic = $this->parseRawResources($aclData['public']);
        // $this->actionDescriptions = $aclData['actionDescriptions'];
        $this->actionDescriptions = [];
    }

    /**
     * Checks if a resource is public
     *
     * @param string $resource
     * @param string $action
     * @return boolean
     */
    public function isPublic($namespace, $resource, $action)
    {
        if (!array_key_exists($namespace, $this->specPublic)) {
            return false;
        } else if (!array_key_exists($resource, $this->specPublic[$namespace])) {
            return false;
        }

        $isPublic = (in_array($action, $this->specPublic[$namespace][$resource]));
        return $isPublic;
    }

    public function splitResource($nsRes)
    {
        $parts = explode(':', $nsRes);
        if (count($parts) == 1) {
            return ['', $parts[0]];
        } else {
            return $parts;
        }
    }

    public function mergeResource($namespace, $resource)
    {
        $nsRes = ($namespace == '') ? $resource : $namespace . ':' . $resource;
        return $nsRes;
    }

    /**
     * Checks if the current role is allowed to access a resource
     *
     * @return boolean
     */
    public function isAllowed()
    {
        $argc = func_num_args();
        $argv = func_get_args();
        if ($argc < 3 || $argc > 4) {
            throw new \Exception('Invalid number of arguments');
        }

        // Parse parameters
        if ($argc === 3) {
            list($role, $nsRes, $action) = $argv;
        } else {
            list($role, $namespace, $resource, $action) = $argv;
            $nsRes = $this->mergeResource($namespace, $resource);
        }

        if (!$this->getAcl()->isRole($role)) {
            return false;
        } else if (!$this->getAcl()->isResource($nsRes)) {
            return false;
        }

        return $this->getAcl()->isAllowed($role, $nsRes, $action);
    }

    /**
     * Returns the permissions assigned to a role
     *
     * @param Roles $roles
     * @return array
     */
    public function getPermissions(Roles $role)
    {
        $permissions = array();
        foreach ($role->getPermissions() as $permission) {
            $permissions[$permission->namespace . ':' . $permission->resource . '.' . $permission->action] = true;
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

    /**
     * Returns all the resources and their actions available in the application
     *
     * @return array
     */
    public function getPrivateSpec()
    {
        if (isset($this->privateResources)) {
            return $this->privateResources;
        }

        $this->privateResources = [];
        foreach ($this->specComplete as $namespace => $resourceArr) {
            $privateTempArr = [];
            foreach ($resourceArr as $resource => $actionArr) {
                $actionPublicArr = (isset($this->specPublic[$namespace][$resource]))
                    ? $this->specPublic[$namespace][$resource] : [];

                $actionPrivateArr = array_diff($actionArr, $actionPublicArr);

                if (!empty($actionPrivateArr)) {
                    $privateTempArr[$resource] = $actionPrivateArr;
                }

            }
            if (!empty($privateTempArr)) {
                $this->privateResources[$namespace] = $privateTempArr;
            }
        }

        return $this->privateResources;
    }

    /**
     * Returns the action description according to its simplified name
     *
     * @param string $action
     * @return $action
     */
    public function getActionDescription($action)
    {
        return $action;
        // return (isset($this->actionDescriptions[$action])) ? $this->actionDescriptions[$action] : $action;
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

        $this->acl = $this->build();
        return $this->acl;
    }

    private function build()
    {
        $acl = new AclMemory();
        $acl->setDefaultAction(\Phalcon\Acl::DENY);

        foreach ($this->specComplete as $namespace => $resources) {
            foreach ($resources as $resource => $actions) {
                $nsRes = ($namespace == '') ? $resource : $namespace . ':' . $resource;
                $acl->addResource(new AclResource($this->mergeResource($namespace, $resource)), $actions);
            }
        }

        // Register roles
        $roles = Roles::find('active = "Y"');
        foreach ($roles as $role) {
            $acl->addRole(new AclRole($role->name));
        }
        // Grant access to private area
        foreach ($roles as $role) {
            foreach ($this->specPublic as $namespace => $resources) {
                foreach ($resources as $resource => $actions) {
                    $acl->allow($role->name, $this->mergeResource($namespace, $resource), $actions);
                }
            }
            // Grant permissions in "permissions" model
            foreach ($role->getPermissions() as $permission) {
                $acl->allow($role->name, $permission->getNamespaceResource(), $permission->action);
            }
        }

        return $acl;
    }


    protected function parseRawResources($rawResources)
    {
        return $rawResources;
    }

}
