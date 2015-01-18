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



    protected function addSoftDeleteBehavior(array $options)
    {
        $options['beforeSoftDelete'] = \Closure::bind(function() {
            $this->_isSoftDeleting = true;
        }, $this, $this);

        $options['afterSoftDelete'] = \Closure::bind(function() {
            $this->_isSoftDeleting = true;
            if (method_exists($this, 'afterDelete')) {
                $this->afterDelete();
            }
        }, $this, $this);

        $this->addBehavior(new SoftDelete($options));
    }

}
