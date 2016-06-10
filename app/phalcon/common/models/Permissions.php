<?php
namespace Webird\Models;

use Phalcon\Validation,
    Phalcon\Validation\Validator\Regex as RegexValidator,
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

    /**
     *
     */
    public static function findFirstByQualified($qualified)
    {
        if (preg_match('/^([a-z]*)::([a-zA-Z]+)::([a-zA-Z]+)$/', $qualified, $matches) !== 1) {
            throw new \Exception('Error: The fully qualified permission is not valid');
        }

        return self::findFirst([
            'conditions' => 'namespace = :namespace: AND resource = :resource: AND action = :action:',
            'bind' => [
                'namespace' => $matches[1],
                'resource'  => $matches[2],
                'action'    => $matches[3]
            ]
        ]);
    }

    /**
     *
     */
    public function getQualified()
    {
        return $this->namespace . '::' . $this->resource . '::' . $this->action;
    }

    /**
     *
     */
    public function getNamespaceResource()
    {
        return ($this->namespace == '') ? $this->resource : $this->namespace . '::' . $this->resource;
    }

    /**
     *
     */
    public function validation()
    {
        $translate = $this->getDI()
            ->getTranslate();

        $validator = new Validation();

        $validator->add('namespace', new RegexValidator([
            'pattern' => '/^([a-z]*)$/',
            'message' => $translate->gettext('Invalid namespace.')
        ]));
        $validator->add('resource', new RegexValidator([
            'pattern' => '/^([a-zA-Z]+)$/',
            'message' => $translate->gettext('Invalid resource.')
        ]));
        $validator->add('action', new RegexValidator([
            'pattern' => '/^([a-zA-Z]+)$/',
            'message' => $translate->gettext('Invalid action.')
        ]));

        return $this->validate($validator);
    }

    /**
     *
     */
    public function initialize()
    {
        $this->belongsTo('rolesId', 'Webird\Models\Roles', 'id', [
            'alias' => 'role'
        ]);
    }
}
