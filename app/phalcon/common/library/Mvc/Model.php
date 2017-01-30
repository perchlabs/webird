<?php
namespace Webird\Mvc;

use Phalcon\Mvc\Model as PhModel;
use Webird\Mvc\Model\Traits\SoftDeleteModelTrait;

/**
 *
 */
class Model extends PhModel
{
    use SoftDeleteModelTrait;

    /**
     *
     */
    public function getMessages($filter = null)
    {
        $messages = parent::getMessages($filter);
        return (is_array($messages)) ? $messages : [];
    }

}
