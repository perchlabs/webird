<?php
namespace Webird\Modules\Admin\Controllers;

use Phalcon\Tag,
    Phalcon\Mvc\Model\Criteria,
    Phalcon\Paginator\Adapter\Model as Paginator,
    Webird\Mvc\Controller,
    Webird\Models\Users,
    Webird\Models\EmailConfirmations,
    Webird\Models\PasswordChanges,
    Webird\Modules\Admin\Forms\UsersForm;

/**
 * Webird\Controllers\UsersController
 * CRUD to manage users
 */
class UsersController extends Controller
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
        $form = new UsersForm();
        $form->setDI($this->getDI());
        $this->view->form = $form;

        $this->persistent->conditions = null;
    }

    /**
     * Searches for users
     */
    public function searchAction()
    {
        $numberPage = 1;
        if ($this->request->isPost()) {
            $query = Criteria::fromInput($this->di, 'Webird\Models\Users', $this->request->getPost());
            $this->persistent->searchParams = $query->getParams();
        } else {
            $numberPage = $this->request->getQuery("page", "int");
        }

        $parameters = [];
        if ($this->persistent->searchParams) {
            $parameters = $this->persistent->searchParams;
        }

        $users = Users::find($parameters);
        if (count($users) == 0) {
            $this->flash->notice($this->translate->gettext('The search did not find any users'));
            return $this->dispatcher->forward([
                "action" => "index"
            ]);
        }

        $paginator = new Paginator([
            "data" => $users,
            "limit" => 10,
            "page" => $numberPage
        ]);

        $this->view->page = $paginator->getPaginate();
    }

    /**
     * Creates a User
     */
    public function createAction()
    {
        $form = new UsersForm();
        $form->setDI($this->getDI());

        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) !== false) {

                $active = $this->request->getPost('active', 'striptags');
                if ($active !== 'Y') {
                    $active = 'N';
                }
                $user = new Users([
                    'name' => $this->request->getPost('name', 'striptags'),
                    'rolesId' => $this->request->getPost('rolesId', 'int'),
                    'email' => $this->request->getPost('email', 'email'),
                    'active' => $active
                ]);

                if ($user->save()) {
                    // The user selected to send an email confirmation
                    $emailExtraMsg = $this->request->getPost('emailActivationMsg', 'striptags', '');
                    $emailExtraMsg = trim($emailExtraMsg);
                    if ($emailExtraMsg != '') {
                        $emailConfirmation = new EmailConfirmations();
                        $emailConfirmation->usersId = $user->id;
                        $emailConfirmation->extraMsg = $emailExtraMsg;
                        if ($emailConfirmation->save()) {
                            $this->flash->notice(
                                sprintf($this->translate->gettext('A confirmation mail has been sent to %s'), $user->email));
                        }
                    }

                    $this->flash->success($this->translate->gettext('User was created successfully. You may add another user.'));
                    Tag::resetInput();
                } else {
                    $this->flash->error($user->getMessages());
                }

            }
        }

        $this->view->form = $form;
    }

    /**
     * Saves the user from the 'edit' action
     */
    public function editAction($id)
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error($this->translate->gettext('User was not found'));
            return $this->dispatcher->forward([
                'action' => 'index'
            ]);
        }
        $this->view->user = $user;

        $form = new UsersForm($user, [
            'edit' => true
        ]);
        $form->setDI($this->getDI());
        $this->view->form = $form;

        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) !== false) {
                $user->assign([
                    'name' => $this->request->getPost('name', 'striptags'),
                    'rolesId' => $this->request->getPost('rolesId', 'int'),
                    'email' => $this->request->getPost('email', 'email'),
                    'banned' => $this->request->getPost('banned'),
                    'active' => $this->request->getPost('active')
                ]);

                if (!$user->save()) {
                    $this->flash->error($user->getMessages());
                } else {
                    $this->flash->success($this->translate->gettext('User was updated successfully'));
                    Tag::resetInput();
                }
            }
        }

    }

    /**
     * Deletes a User
     *
     * @param int $id
     */
    public function deleteAction($id)
    {
        $user = Users::findFirstById($id);
        if (!$user) {
            $this->flash->error($this->translate->gettext('User was not found'));
            return $this->dispatcher->forward([
                'action' => 'index'
            ]);
        }

        if (!$user->delete()) {
            $this->flash->error($user->getMessages());
        } else {
            $this->flash->success($this->translate->gettext('User was deleted'));
        }

        return $this->dispatcher->forward([
            'action' => 'index'
        ]);
    }

}
