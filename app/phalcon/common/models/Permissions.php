<?php
namespace Webird\Models;

use Phalcon\Mvc\Model\Message as Message,
    Phalcon\Mvc\Model\Validator\Regex as RegexValidator,
    Webird\Mvc\Model;

/**
 * Permissions
 * Stores the permissions by role
 */
class Permissions extends Model
{

    /**
     *
     * @var integer
     */
    public $id;

    /**
     *
     * @var integer
     */
    public $rolesId;

    /**
     *
     * @var string
     */
    public $namespace;

    /**
     *
     * @var string
     */
    public $resource;

    /**
     *
     * @var string
     */
    public $action;

    public function getNamespaceResource()
    {
        return ($this->namespace == '') ? $this->resource : $this->namespace . ':' . $this->resource;
    }

    // public function getNamespaceResource()
    // {
    //     if ($this->namespace == '') {
    //         $nsRes =  $this->resource;
    //     } else {
    //         $nsRes = $this->namespace . ':' . $this->resource;
    //     }
    //     return $nsRes;
    // }


    public function validation()
    {
        $this->validate(new RegexValidator([
            'field' => 'namespace',
            'pattern' => '/^([a-z]*)$/'
        ]));
        $this->validate(new RegexValidator([
            'field' => 'resource',
            'pattern' => '/^([a-zA-Z]+)$/'
        ]));
        $this->validate(new RegexValidator([
            'field' => 'action',
            'pattern' => '/^([a-zA-Z]+)$/'
        ]));
        if ($this->validationHasFailed() == true) {
            $message = new Message($this->getDI()->getTranslate()->gettext(
                sprintf('The resource %s has an invalid name', $this->resource)));
            $this->appendMessage($message);
            return false;
        }
    }


    public function initialize()
    {
        $this->belongsTo('rolesId', 'Webird\Models\Roles', 'id', [
            'alias' => 'role'
        ]);
    }
}
