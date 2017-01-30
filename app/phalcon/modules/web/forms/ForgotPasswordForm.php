<?php
namespace Webird\Modules\Web\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Submit;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

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
