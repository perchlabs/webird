<?php
/**
 * Manager.php 2014-08-31 04:11
 * ----------------------------------------------
 *
 *
 * @author      Stanislav Kiryukhin <korsar.zn@gmail.com>
 * @copyright   Copyright (c) 2014, CKGroup.ru
 *
 * @version     0.0.1
 * ----------------------------------------------
 * All Rights Reserved.
 * ----------------------------------------------
 */
namespace Webird\Mailer;

use Phalcon\Config,
    Phalcon\Mvc\User\Component,
    Phalcon\Mvc\View\Simple as ViewSimple,
    Webird\Mailer\Message,
    Swift_SmtpTransport as SmtpTransport,
    Swift_MailTransport as MailTransport,
    Swift_SendmailTransport as SendmailTransport,
    Swift_Mailer as Mailer;

/**
 * Class Manager
 * @package Webird\Manager
 */
class Manager extends Component
{
    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $from;


    /**
     * @var \Swift_Transport
     */
    protected $transport;

    /**
     * @var \Swift_Mailer
     */
    protected $mailer;

    /**
     * Create a new MailerManager component using $config for configuring
     *
     * @param array $config
     */
    public function __construct($config, $from = null)
    {
        $this->configure($config);
        $this->from = $from;
    }

    /**
     * Create a new Message instance.
     *
     * Events:
     * - mailer:beforeCreateMessage
     * - mailer:afterCreateMessage
     *
     * @return \Webird\Mailer\Message
     */
    public function createMessage()
    {
        $eventsManager = $this->getEventsManager();

        if ($eventsManager) {
            $eventsManager->fire('mailer:beforeCreateMessage', $this);
        }

        /** @var $message Message */
        $message = new Message($this);

        // if (($from = $this->getConfig('from'))) {
        //     $message->from($from['email'], isset($from['name']) ? $from['name'] : null);
        // }
        $message->from($this->from['email'], $this->from['name']);


        if ($eventsManager) {
            $eventsManager->fire('mailer:afterCreateMessage', $this, [$message]);
        }

        return $message;
    }

    /**
     * Create a new Message instance.
     * For the body of the message uses the result of render of view
     *
     * Events:
     * - mailer:beforeCreateMessage
     * - mailer:afterCreateMessage
     *
     * @param string $view
     * @param array $params         optional
     * @param null|string $viewsDir optional
     *
     * @return \Webird\Mailer\Message
     *
     * @see \Webird\Mailer\Manager::createMessage()
     */
    public function createMessageFromView($viewPath, $params = [], $viewsDir = null)
    {
        $message = $this->createMessage();
        $message->content($this->renderView($viewPath, $params, $viewsDir), $message::CONTENT_TYPE_HTML);
        return $message;
    }

    /**
     * Return a {@link \Swift_Mailer} instance
     *
     * @return \Swift_Mailer
     */
    public function getSwift()
    {
        return $this->mailer;
    }

    /**
     * Normalize IDN domains.
     *
     * @param $email
     *
     * @return string
     *
     * @see \Webird\Mailer\Manager::punycode()
     */
    public function normalizeEmail($email)
    {
        if (preg_match('#[^(\x20-\x7F)]+#', $email)) {

            list($user, $domain) = explode('@', $email);

            return $user . '@' . $this->punycode($domain);

        } else {
            return $email;
        }
    }

    /**
     * Configure MailerManager class
     *
     * @param array $config
     *
     * @see \Webird\Mailer\Manager::registerSwiftTransport()
     * @see \Webird\Mailer\Manager::registerSwiftMailer()
     */
    protected function configure($config)
    {
        $this->config = $config;

        $this->registerSwiftTransport();
        $this->registerSwiftMailer();
    }

    /**
     * Create a new Driver-mail of SwiftTransport instance.
     *
     * Supported driver-mail:
     * - smtp
     * - sendmail
     * - mail
     *
     */
    protected function registerSwiftTransport()
    {
        switch ($driver = $this->getConfig('driver')) {
            case 'smtp':
                $this->transport = $this->registerTransportSmtp();
                break;
            case 'mail':
                $this->transport = $this->registerTransportMail();
                break;
            case 'sendmail':
                $this->transport = $this->registerTransportSendmail();
                break;
            default:
                throw new \InvalidArgumentException(sprintf('Driver-mail "%s" is not supported', $driver));
        }
    }

    /**
     * Create a new SmtpTransport instance.
     *
     * @return \Swift_SmtpTransport
     *
     * @see \Swift_SmtpTransport
     */
    protected function registerTransportSmtp()
    {
        $config = $this->getConfig();

        /** @var $transport \Swift_SmtpTransport: */
        $transport = (new SmtpTransport())
            ->setHost($config['host'])
            ->setPort($config['port']);

        if (isset($config['encryption'])) {

            $transport->setEncryption($config['encryption']);
        }

        if (isset($config['username'])) {
            $transport->setUsername($this->normalizeEmail($config['username']));
            $transport->setPassword($config['password']);
        }

        return $transport;
    }

    /**
     * Get option config or the entire array of config, if the parameter $key is not specified.
     *
     * @param null $key
     * @param null $default
     *
     * @return string|array
     */
    protected function getConfig($key = null, $default = null)
    {
        if ($key !== null) {
            if (isset($this->config[$key])) {
                return $this->config[$key];
            } else {
                return $default;
            }

        } else {
            return $this->config;
        }
    }

    /**
     * Convert UTF-8 encoded domain name to ASCII
     *
     * @param $str
     *
     * @return string
     */
    protected function punycode($str)
    {
        if (function_exists('idn_to_ascii')) {
            return idn_to_ascii($str);
        } else {
            return $str;
        }
    }

    /**
     * Create a new MailTransport instance.
     *
     * @return \Swift_MailTransport
     *
     * @see \Swift_MailTransport
     */
    protected function registerTransportMail()
    {
        return new MailTransport();
    }

    /**
     * Create a new SendmailTransport instance.
     *
     * @return \Swift_SendmailTransport
     *
     * @see \Swift_SendmailTransport
     */
    protected function registerTransportSendmail()
    {
        /** @var $transport \Swift_SendmailTransport */
        $transport = (new SendmailTransport())
            ->setCommand($this->getConfig('sendmail', '/usr/sbin/sendmail -bs'));

        return $transport;
    }

    /**
     * Register SwiftMailer
     *
     * @see \Swift_Mailer
     */
    protected function registerSwiftMailer()
    {
        $this->mailer = new Mailer($this->transport);
    }

    /**
     * Renders a view
     *
     * @param $viewPath
     * @param $params
     * @param null $viewsDir
     *
     * @return string
     */
    protected function renderView($viewPath, $params, $viewsDir = null)
    {
        $config = $this->getDI()->get('config');

        $view = $this->getView();
        $content = $view->render("email/$viewPath", $params);

        return $content;
    }

    /**
     * Return a {@link \Phalcon\Mvc\View\Simple} instance
     *
     * @return \Phalcon\Mvc\View\Simple
     */
    protected function getView()
    {
        $di = $this->getDI();
        $config = $di->get('config');

        $view = $di->get('template');

        return $view;
    }
}
