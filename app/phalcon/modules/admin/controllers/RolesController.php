<?php
namespace Webird\Modules\Admin\Controllers;

use Phalcon\Tag;
use Phalcon\Mvc\Model\Criteria;
use Phalcon\Paginator\Adapter\Model as Paginator;
use Webird\Mvc\Controller;
use Webird\Models\Roles;
use Webird\Modules\Admin\Forms\RolesForm;

/**
 * Webird\Controllers\RolesController
 * CRUD to manage roles
 */
class RolesController extends Controller
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
     * Default action, shows the search form
     */
    public function indexAction()
    {
        $form = new RolesForm();
        $form->setDI($this->getDI());

        $this->persistent->conditions = null;
        $this->view->form = $form;
    }

    /**
     * Searches for roles
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Webird\Models\Roles', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = [];
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $roles = Roles::find($parameters);
        if (count($roles) == 0) {

            $this->flash->notice($this->translate->gettext('The search did not find any roles'));

            return $this->dispatcher->forward([
                "action" => "index"
            ]);
        }

        $paginator = new Paginator([
            "data" => $roles,
            "limit" => 10,
            "page" => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Creates a new Role
     */
    public function createAction()
    {
        $form = new RolesForm(null);
        $form->setDI($this->getDI());
        $this->view->form = $form;

        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) !== false) {
                $role = new Roles([
                    'name' => $this->request->getPost('name', 'striptags'),
                    'active' => $this->request->getPost('active')
                ]);

                if (!$role->save()) {
                    $this->flash->error($role->getMessages());
                } else {
                    $this->flash->success($this->translate->gettext('Role was created successfully'));
                }

                Tag::resetInput();
            }
        }
    }

    /**
     * Edits an existing Role
     *
     * @param int $id
     */
    public function editAction($id)
    {
        $role = Roles::findFirstById($id);
        if (!$role) {
            $this->flash->error($this->translate->gettext('Role was not found'));
            return $this->dispatcher->forward([
                'action' => 'index'
            ]);
        }
        $this->view->role = $role;

        $form = new RolesForm($role, [
            'edit' => true
        ]);
        $form->setDI($this->getDI());
        $this->view->form = $form;

        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) !== false) {
                $role->assign([
                    'name' => $this->request->getPost('name', 'striptags'),
                    'active' => $this->request->getPost('active')
                ]);

                if (!$role->save()) {
                    $this->flash->error($role->getMessages());
                } else {
                    $this->flash->success($this->translate->gettext('Role was updated successfully'));
                }

                Tag::resetInput();
            }
        }

    }

    /**
     * Deletes a Role
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $role = Roles::findFirstById($id);
        if (!$role) {

            $this->flash->error($this->translate->gettext('Role was not found'));

            return $this->dispatcher->forward([
                'action' => 'index'
            ]);
        }

        if (!$role->delete()) {
            $this->flash->error($role->getMessages());
        } else {
            $this->flash->success($this->translate->gettext('Role was deleted'));
        }

        return $this->dispatcher->forward([
            'action' => 'index'
        ]);
    }
}
