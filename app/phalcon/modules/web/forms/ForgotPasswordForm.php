<?php
namespace Webird\Modules\Web\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email;

/**
 * Form for requesting a password reset
 */
class ForgotPasswordForm extends Form
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
        $email->setLabel($t->gettext('Email'));
        $email->addValidators([
            new PresenceOf([
                'message' => $t->gettext('Email is required')
            ]),
            new Email([
                'message' => $t->gettext('Email is not valid')
            ])
        ]);
        $this->add($email);

        $this->add(new Submit($t->gettext('send'), [
            'class' => 'btn btn-primary'
        ]));
    }
}
