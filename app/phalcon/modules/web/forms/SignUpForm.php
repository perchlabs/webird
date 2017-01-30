<?php
namespace Webird\Modules\Web\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Password;
use Phalcon\Forms\Element\Submit;
use Phalcon\Forms\Element\Check;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;
use Phalcon\Validation\Validator\Identical;
use Phalcon\Validation\Validator\StringLength;
use Phalcon\Validation\Validator\Confirmation;

/**
 * Form for new user registration
 */
// TODO, FIXME This needs to evaluated
class SignUpForm extends Form
{
    /**
     * Form configuration
     */
    public function initialize($entity = null, $options = null)
    {
        $t = $this->getDI()->get('translate');
        $passwordMinLength = $this->config->security->passwordMinLength;

        // Full User Name
        $name = new Text('name');
        $name->setLabel($t->gettext('Name'));
        $name->addValidators([
            new PresenceOf([
                'message' => $t->gettext('Full name is required'),
            ]),
        ]);
        $this->add($name);

        // Email
        $email = new Text('email');
        $email->setLabel($t->gettext('E-Mail'));
        $email->addValidators([
            new PresenceOf([
                'message' => $t->gettext('Email is required'),
            ]),
            new Email([
                'message' => t$->gettext('Email is not valid'),
            ]),
        ]);
        $this->add($email);

        // Password
        $password = new Password('password');
        $password->setLabel($t->gettext('Password'));
        $password->addValidators([
            new PresenceOf([
                'message' => $t->gettext('The password is required'),
            ]),
            new StringLength([
                'min' => $passwordMinLength,
                'messageMinimum' => sprintf($t->gettext('Password is too short. Minimum %d characters'), $passwordMinLength),
            ]),
            new Confirmation([
                'message' => $t->gettext('Password doesn\'t match confirmation'),
                'with' => 'confirmPassword',
            ]),
        ]);
        $this->add($password);

        // Confirm Password
        $confirmPassword = new Password('confirmPassword');
        $confirmPassword->setLabel($t->gettext('Confirm Password'));
        $confirmPassword->addValidators([
            new PresenceOf([
                'message' => $t->gettext('The confirmation password is required'),
            ]),
        ]);
        $this->add($confirmPassword);

        // Terms
        $terms = new Check('terms', [
            'value' => $t->gettext('yes'),
        ]);
        $terms->setLabel($t->gettext('Accept terms and conditions'));
        $terms->addValidator(new Identical([
            'value' => 'yes',
            'message' => $t->gettext('Terms and conditions must be accepted'),
        ]));
        $this->add($terms);

        // CSRF
        $csrf = new Hidden('csrf');
        $csrf->addValidator(new Identical([
            'value' => $this->security->getSessionToken(),
            'message' => $t->gettext('CSRF validation failed'),
        ]));
        $this->add($csrf);

        // Sign Up
        $this->add(new Submit($t->gettext('Sign Up'), [
            'class' => 'btn btn-success',
        ]));
    }

    /**
     * Prints messages for a specific element
     */
    public function messages($name)
    {
        if ($this->hasMessagesFor($name)) {
            foreach ($this->getMessagesFor($name) as $message) {
                $this->flash->error($message);
            }
        }
    }
}
