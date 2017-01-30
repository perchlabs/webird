<?php
namespace Webird\Modules\Admin\Forms;

use Phalcon\Forms\Form;
use Phalcon\Forms\Element\Text;
use Phalcon\Forms\Element\Hidden;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Submit;
use Phalcon\Validation\Validator\PresenceOf;
use Phalcon\Validation\Validator\Email;

/**
 * Form for changing the user permission role
 */
class RolesForm extends Form
{
    /**
     * Form configuration
     */
    public function initialize($entity = null, $options = null)
    {
        $t = $this->getDI()->get('translate');

        if (isset($options['edit']) && $options['edit']) {
            $id = new Hidden('id');
        } else {
            $id = new Text('id');
        }
        $id->setLabel($t->gettext('Id'));
        $this->add($id);

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

        $active = new Select('active', [
            'Y' => $t->gettext('Yes'),
            'N' => $t->gettext('No')
        ]);
        $active->setLabel($t->gettext('Active'));
        $this->add($active);

        // Submit
        $submit = new Submit('submit', [
            'value' => $t->gettext('Save')
        ]);
        $this->add($submit);
    }
}
