<?php
namespace Webird\Models;

use Webird\Mvc\Model;

/**
 * RememberTokens
 * Stores the remember me tokens
 */
class RememberTokens extends Model
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
    public $token;

    /**
     *
     * @var string
     */
    public $userAgent;

    /**
     *
     * @var integer
     */
    public $createdAt;

    /**
     * Before create the user assign a password
     */
    protected function beforeValidationOnCreate()
    {
        // Timestamp the confirmaton
        $this->createdAt = time();
    }

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
