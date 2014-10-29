<?php
namespace Webird\Admin\Forms;

use Phalcon\Forms\Form,
    Phalcon\Forms\Element\Text,
    Phalcon\Forms\Element\Textarea,
    Phalcon\Forms\Element\Hidden,
    Phalcon\Forms\Element\Select,
    Phalcon\Forms\Element\Check,
    Phalcon\Forms\Element\Submit,
    Phalcon\Validation\Validator\PresenceOf,
    Phalcon\Validation\Validator\Email,
    Webird\Models\Roles;

/**
 * Form for modifying an user
 */
class UsersForm extends Form
{

    /**
     * Form configuration
     */
    public function initialize($entity = null, $options = null)
    {
        $t = $this->getDI()->get('translate');

        // In edition the id is hidden
        if (isset($options['edit']) && $options['edit']) {
            $id = new Hidden('id');
        } else {
            $id = new Text('id');
        }
        $id->setLabel($t->gettext('Id'));
        $this->add($id);

        // Name field
        $name = new Text('name', [
            'placeholder' => $t->gettext('Name')
        ]);
        $name->setLabel($t->gettext('Name'));
        $name->addValidators([
            new PresenceOf([
                'message' => $t->gettext('Name is required')
            ])
        ]);
        $this->add($name);

        // Email field
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

        // rolesId field
        $role = new Select('rolesId', Roles::find('active = "Y"'), [
            'using' => [
                'id',
                'name'
            ],
            'useEmpty' => true,
            'emptyText' => '...',
            'emptyValue' => ''
        ]);
        $role->setLabel($t->gettext('Role'));
        $role->addValidators([
            new PresenceOf([
                'message' => $t->gettext('The user role must be set.')
            ])
        ]);
        $this->add($role);

        // active field
        $active = new Select('active', [
            'N' => $t->gettext('No'),
            'Y' => $t->gettext('Yes')
        ]);
        $active->setLabel($t->gettext('Active'));
        $this->add($active);

        // banned field
        $banned = new Select('banned', [
            'Y' => $t->gettext('Yes'),
            'N' => $t->gettext('No')
        ]);
        $banned->setLabel($t->gettext('Banned'));
        $this->add($banned);

        // emailActivationMsg field
        $emailExtraMsg = new Textarea('emailActivationMsg', [
            'placeholder' => $t->gettext('Add text to send confirmation email.')
        ]);
        $emailExtraMsg->setLabel($t->gettext('Send activation email'));
        $this->add($emailExtraMsg);

        // Submit
        $submit = new Submit('submit', [
            'value' => $t->gettext('Save')
        ]);
        $this->add($submit);
    }
}
