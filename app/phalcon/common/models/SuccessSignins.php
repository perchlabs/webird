<?php
namespace Webird\Models;

use Webird\Mvc\Model;

/**
 * SuccessSignins
 * This model registers successfull signins registered users have made
 */
class SuccessSignins extends Model
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
     * @var string
     */
    public $method;

    /**
     *
     * @var string
     */
    public $userAgent;

    /**
     *
     */
    public function initialize()
    {
        $this->belongsTo('usersId', 'Webird\Models\Users', 'id', [
            'alias' => 'user'
        ]);
    }
}
