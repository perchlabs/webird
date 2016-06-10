<?php
namespace Webird\Modules\Web\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Password,
    Phalcon\Forms\Element\Submit,
    Phalcon\Forms\Element\Check,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Phalcon\Validation\Validator\Identical;

/**
 * Form for user signin
 */
class SigninForm extends Form
{
    /**
     * Form configuration
     */
    public function initialize()
    {
        $t = $this->getDI()->get('translate');

        // Email
        $email = new Text('email', [
            'placeholder' => $t->gettext('Email')
        ]);
        $email->addValidators([
            new PresenceOf([
                'message' => $t->gettext('Email is required')
            ]),
            new Email([
                'message' => $t->gettext('Email is not valid')
            ])
        ]);
        $this->add($email);

        // Password
        $password = new Password('password', [
            'placeholder' => $t->gettext('Password')
        ]);
        $password->addValidator(new PresenceOf([
            'message' => $t->gettext('Password is required')
        ]));
        $this->add($password);

        // Remember
        $remember = new Check('remember', [
            'value' => $t->gettext('yes')
        ]);
        $remember->setLabel($t->gettext('Remember me'));
        $this->add($remember);

        // Submit
        $submit = new Submit('submit', [
            'value' => $t->gettext('Go')
        ]);
        $this->add($submit);
    }
}
