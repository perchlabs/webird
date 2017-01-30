<?php
namespace Webird\Modules\Admin\Controllers;

use Webird\Mvc\Controller;
use Webird\Models\Roles;
use Webird\Models\Permissions;

/**
 * View and define permissions for the various role levels.
 */
class PermissionsController extends Controller
{

    /**
     * Default action. Set the private (authenticated) layout (layouts/admin.volt)
     */
    public function initialize()
    {
        parent::initialize();
        $this->view->setTemplateBefore('admin');
    }

    /**
     * View the permissions for a role level, and change them if we have a POST.
     */
    public function indexAction()
    {
        if ($this->request->isPost()) {

            // Validate the role
            $role = Roles::findFirstById($this->request->getPost('roleId'));
            if (!$role) {
                $this->flash->error($this->translate->gettext('The role could not be found.'));
                return;
            }

            if ($this->request->hasPost('save') && $this->request->hasPost('permissions')) {
                $this->updateRolePermissions($role);
            }

            $this->view->acl = $this->acl;
            $this->view->role = $role;
            $this->view->permissions = $this->acl->getPermissions($role);
        }

        // Pass all the active roles
        $this->view->roles = Roles::find([
            'active = :active:',
            'bind' => [
                'active' => 'Y',
            ],
        ]);
    }

    /**
     *
     */
    private function updateRolePermissions(Roles $role)
    {
        $postPermissions = $this->request->getPost('permissions');
        $existingPermissions = [];
        foreach ($role->getPermissions() as $permission) {
            $existingPermissions[] = $permission->getQualified();
        }

        $addList = array_diff($postPermissions, $existingPermissions);
        $deleteList = array_diff($existingPermissions, $postPermissions);
        $deletedError = false;
        $savedError = false;

        foreach ($deleteList as $qualified) {
            $permission = Permissions::findFirstByQualified($qualified);
            if (!$permission->delete()) {
                $deletedError = true;
            }
        }

        foreach ($addList as $qualified) {
            if (preg_match('/^([a-z]*)::([a-zA-Z]+)::([a-zA-Z]+)$/', $qualified, $matches) !== 1) {
                throw new \Exception('The fully qualified permission is not valid');
            }

            $permission = new Permissions();
            $permission->rolesId = $role->id;
            $permission->namespace = $matches[1];
            $permission->resource = $matches[2];
            $permission->action = $matches[3];

            if (!$permission->save()) {
                $savedError = true;
            }
        }

        if ($deletedError || $savedError) {
            $this->flash->success($this->translate->gettext('Permissions were updated with success'));
        }
    }
}
