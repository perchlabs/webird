<?php
namespace Webird\Web\Controllers;

use Phalcon\Tag,
    Webird\Controllers\BaseController,
    Webird\Models\Users,
    Webird\Models\EmailConfirmations,
    Webird\Models\ResetPasswords,
    Webird\Models\PasswordChanges,
    Webird\Forms\ChangePasswordForm,
    Webird\Web\Forms\ForgotPasswordForm;

/**
 * SettingsController
 * Provides help to users to confirm their passwords or reset them
 */
class SettingsController extends BaseController
{

    public function initialize()
    {
        parent::initialize();

        $this->view->setTemplateBefore('private');
    }

    public function indexAction()
    {

    }

    /**
     * Signed in users can change their password
     */
    public function changePasswordAction()
    {
        $user = $this->auth->getUser();
        if ($user === false) {
            $this->flash->success($this->translate->gettext('You must be signed in to change the password'));
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'notification'
            ]);
        }

        $form = new ChangePasswordForm();
        $form->setDI($this->getDI());

        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) !== false) {
                $user->password = $this->request->getPost('password');
                $user->mustChangePassword = 'N';

                $passwordChange = new PasswordChanges();
                $passwordChange->user = $user;
                $passwordChange->ipAddress = $this->request->getClientAddress();
                $passwordChange->userAgent = $this->request->getUserAgent();

                if ($passwordChange->save()) {
                    $this->auth->clearNeedToChangePassword();
                    $this->flash->success($this->translate->gettext('Your password was successfully changed'));
                    Tag::resetInput();
                } else {
                    $this->flash->error($passwordChange->getMessages());
                }
            }
        }

        $this->view->form = $form;
    }

}
