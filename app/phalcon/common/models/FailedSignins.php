<?php
namespace Webird\Models;

use Webird\Mvc\Model;

/**
 * FailedSignins
 * This model registers unsuccessfull signins registered and non-registered users have made
 */
class FailedSignins extends Model
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
    public $usersId;

    /**
     *
     * @var string
     */
    public $ipAddress;

    /**
     *
     * @var integer
     */
    public $attempted;

    /**
     *
     */
    public function initialize()
    {
        $this->belongsTo('usersId', 'Webird\Models\Users', 'id', [
            'alias' => 'user',
        ]);
    }
}
