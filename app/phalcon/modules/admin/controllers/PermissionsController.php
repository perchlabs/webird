<?php
namespace Webird\Admin\Controllers;

use Webird\Mvc\Controller,
    Webird\Models\Roles,
    Webird\Models\Permissions;

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

            if ($role) {
                if ($this->request->hasPost('save') && $this->request->hasPost('permissions')) {

                    // Deletes the current permissions
                    $role->getPermissions()->delete();

                    $savedMessages = [];
                    // Save the new permissions
                    foreach ($this->request->getPost('permissions') as $permission) {
                        // This may become out of sync with the permission model validators but
                        // its user input so rather be a bit more careful.
                        if (preg_match('/^([a-z]*):([a-zA-Z]+).([a-zA-Z]+)$/', $permission, $matches) !== 1) {
                            throw new \Exception('Error: The fully qualified permission is not valid');
                        }

                        $permission = new Permissions();
                        $permission->rolesId = $role->id;
                        $permission->namespace = $matches[1];
                        $permission->resource = $matches[2];
                        $permission->action = $matches[3];

                        if (!$permission->save()) {
                            $savedMessages[] = $permission->getMessages()[0];
                        }
                    }

                    if (empty($savedMessages)) {
                        $this->flash->success($this->translate->gettext('Permissions were updated with success'));
                    }
                }

                $this->view->acl = $this->acl;
                // // Pass the current permissions to the view
                $this->view->permissions = $this->acl->getPermissions($role);
            }

            $this->view->role = $role;
        }

        // Pass all the active roles
        $this->view->roles = Roles::find('active = "Y"');
    }
}
