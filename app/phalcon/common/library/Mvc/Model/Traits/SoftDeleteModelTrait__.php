<?php
namespace Webird\Mvc\Model\Traits;

use Webird\Mvc\Model\Behavior\SoftDelete;

trait SoftDeleteModelTrait
{

    private $_isSoftDeleting = false;

    protected function isSoftDeleting()
    {
        return $this->_isSoftDeleting;
    }



    protected function addSoftDeleteBehavior($field, $deleteValue = 'N')
    {
        $this->addBehavior(new SoftDelete([
            'field'            => $field,
            'deleteValue'      => $deleteValue,
            'cascadeDelete'    => true,
            'beforeSoftDelete' => \Closure::bind($this->beforeSoftDelete, $this, $this),
            'afterSoftDelete'  => \Closure::bind($this->afterSoftDelete, $this, $this)
        ]));
    }


    public function beforeSoftDelete()
    {
        $this->_isSoftDeleting = true;

        if (method_exists($this, 'beforeDelete')) {
            $this->beforeDelete();
        }
    }


    public function afterSoftDelete()
    {
        // Force the event to fire since the delete operation is being skipped over
        if (method_exists($this, 'afterDelete')) {
            $this->afterDelete();
        }

        $this->_isSoftDeleting = false;
    }

}
