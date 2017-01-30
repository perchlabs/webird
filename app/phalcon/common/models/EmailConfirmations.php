<?php
namespace Webird\Models;

use Webird\Mvc\Model;

/**
 * EmailConfirmations
 * Stores the reset password codes and their evolution
 */
class EmailConfirmations extends Model
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
     */
    public $code;

    /**
     *
     * @var integer
     */
    public $createdAt;

    /**
     *
     * @var integer
     */
    public $modifiedAt;

    /**
     *
     */
    public $confirmed;

    /**
     * An extra message to include in an email.  This variable is skipped for the DB
     *
     * @var string
     */
    public $extraMsg;

    /**
     * Before create the user assign a password
     */
    protected function beforeValidationOnCreate()
    {
        // Timestamp the confirmaton
        $this->createdAt = time();

        // Generate a random confirmation code
        $this->code = preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));

        // Set status to non-confirmed
        $this->confirmed = 'N';
    }

    /**
     * Sets the timestamp before update the confirmation
     */
    protected function beforeValidationOnUpdate()
    {
        // Timestamp the confirmaton
        $this->modifiedAt = time();
    }

    /**
     * Send a confirmation e-mail to the user after create the account
     */
    protected function afterCreate()
    {
        $config = $this->getDI()->get('config');
        $translate = $this->getDI()->get('translate');

        $subjectMsg = sprintf($translate->gettext('Please confirm your email on %s'),
            $config->site->domains[0]);

        $message = $this->getDI()
            ->getMailer()
            ->createMessageFromView('emailConfirmation', [
                'extraMsg' => isset($this->extraMsg) ? $this->extraMsg : '',
                'resetUrl' => 'confirm/' . $this->code,
            ])
            ->to($this->user->email, $this->user->name)
            ->subject($subjectMsg);

        $message->send();
    }

    /**
     *
     */
    public function initialize()
    {
        $this->belongsTo('usersId', 'Webird\Models\Users', 'id', [
            'alias' => 'user',
        ]);

        $this->skipAttributes(['extraMessage']);
    }
}
