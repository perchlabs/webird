<?php
namespace Webird\Modules\Web\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Password,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\StringLength,
    Phalcon\Validation\Validator\Confirmation;

/**
 * Form for changing user password
 */
class ChangePasswordForm extends Form
{
    /**
     * Form configuration
     */
    public function initialize()
    {
        $t = $this->getDI()->get('translate');
        $passwordMinLength = $this->config->security->passwordMinLength;

        // Password
        $password = new Password('password');
        $password->setLabel($t->gettext('Password'));
        $password->addValidators([
            new PresenceOf([
                'message' => $t->gettext('Password is required')
            ]),
            new StringLength([
                'min' => $passwordMinLength,
                'messageMinimum' => sprintf($t->gettext('Password is too short. Minimum %d characters'), $passwordMinLength)
            ]),
            new Confirmation([
                'message' => $t->gettext('Password doesn\'t match confirmation'),
                'with' => 'confirmPassword'
            ])
        ]);
        $this->add($password);

        // Confirm Password
        $confirmPassword = new Password('confirmPassword');
        $confirmPassword->setLabel($t->gettext('Confirm Password'));
        $confirmPassword->addValidators([
            new PresenceOf([
                'message' => $t->gettext('The confirmation password is required')
            ])
        ]);
        $this->add($confirmPassword);
    }
}
