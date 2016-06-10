<?php
namespace Webird\Modules\Web\Controllers;

use Webird\Mvc\Controller,
    Webird\Auth\AuthMustChangePasswordException,
    Webird\Models\Users,
    Webird\Models\EmailConfirmations,
    Webird\Models\ResetPasswords,
    Webird\Auth\AuthInactiveUserException,
    Webird\Modules\Web\Forms\ChangePasswordForm,
    Webird\Modules\Web\Forms\ForgotPasswordForm;

/**
 * UserspublicController
 * Provides help to users to confirm their passwords or reset them
 */
class UserspublicController extends Controller
{

    public function initialize()
    {
        parent::initialize();
    }

    public function indexAction()
    {

    }

    /**
     * Confirms an e-mail, if the user must change thier password then changes it
     */
    public function confirmEmailAction()
    {
        $code = $this->dispatcher->getParam('code');
        $t = $this->translate;

        $confirmation = EmailConfirmations::findFirstByCode($code);
        if (!$confirmation) {
            $this->flash->error($t->gettext('The confirmation code was not valid.'));
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'notification'
            ]);
        }

        if ($confirmation->user->isBanned()) {
            $this->flash->error($t->gettext('User is banned'));
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'notification'
            ]);
        }

        if ($confirmation->confirmed != 'N') {
            $this->flash->notice($t->gettext("You have already confirmed your email. Proceed to signin"));
            return $this->dispatcher->forward([
                'controller' => 'session',
                'action' => 'signin'
            ]);
        }

        $confirmation->confirmed = 'Y';
        $confirmation->user->active = 'Y';

        /**
         * Change the confirmation to 'confirmed' and update the user to 'active'
         */
        if (!$confirmation->save()) {
            foreach ($confirmation->getMessages() as $message) {
                $this->flash->error($message);
            }
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'notification'
            ]);
        }

        /**
         * Identify the user in the application
         */
        try {
            $this->auth->authUserById($confirmation->user->id, 'email_confirm');
            $this->flash->success($this->translate->gettext('The email was successfully confirmed'));
            return $this->response->redirect($this->config->app->defaultPath);
        } catch (AuthMustChangePasswordException $e) {
            return $this->response->redirect('settings/changePassword');
        }
    }

    /**
     * Resets the users password if a password reset exists in the database.
     */
    public function resetPasswordAction()
    {
        $t = $this->translate;

        $code = $this->dispatcher->getParam('code');

        $resetPassword = ResetPasswords::findFirstByCode($code);
        if (!$resetPassword) {
            $this->flash->error($this->translate->gettext('The password reset code is invalid'));
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'notification'
            ]);
        }

        $user = $resetPassword->user;
        if (!$user->isActive()) {
            $this->flash->error($t->gettext('User is inactive'));
            $this->flash->notice($t->gettext('Activate the user first before changing password.'));
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'notification'
            ]);
        } else if ($user->isBanned()) {
            $this->flash->error($t->gettext('User is banned'));
            return $this->dispatcher->forward([
                'controller' => 'index',
                'action' => 'notification'
            ]);
        }

        $form = new ChangePasswordForm();
        $form->setDI($this->getDI());
        $this->view->form = $form;
        $this->view->setVar('email', $user->email);

        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) !== false) {
                $user->password = $this->request->getPost('password');
                $user->mustChangePassword = 'N';
                $resetPassword->reset = 'Y';
                try {
                    if ($resetPassword->save()) {
                        // Authenticate the user
                        $this->auth->clearNeedToChangePassword();
                        $this->auth->authUserById($resetPassword->usersId, 'pw_reset');
                        return $this->response->redirect($this->config->app->defaultPath);
                    } else {
                        foreach ($resetPassword->getMessages() as $message) {
                            $this->flash->error($message);
                        }
                        return $this->dispatcher->forward([
                            'controller' => 'index',
                            'action' => 'notification'
                        ]);
                    }
                } catch (AuthException $e) {
                    $this->flash->error($e->getMessage());
                }
            }
        }

    }

    /**
     * Shows the forgot password form
     */
    public function forgotPasswordAction()
    {
        $form = new ForgotPasswordForm();
        $form->setDI($this->getDI());

        if ($this->request->isPost()) {
            if ($form->isValid($this->request->getPost()) !== false) {
                $user = Users::findFirstByEmail($this->request->getPost('email'));
                if (!$user) {
                    $this->flash->success($this->translate->gettext('There is no account associated with this email.'));
                } else {
                    $resetPassword = new ResetPasswords();
                    $resetPassword->usersId = $user->id;
                    if ($resetPassword->save()) {
                        $this->flash->success($this->translate->gettext('An email has been sent!'));
                        $this->flash->success($this->translate->gettext('Please check your inbox for a reset password message.'));
                        return $this->dispatcher->forward([
                            'controller' => 'index',
                            'action' => 'notification'
                        ]);
                    } else {
                        foreach ($resetPassword->getMessages() as $message) {
                            $this->flash->error($message);
                        }
                    }
                }
            }
        }

        $this->view->form = $form;
    }

}
