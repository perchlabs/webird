<?php
namespace Webird\Http;

use Phalcon\DiInterface;
use Phalcon\Di\InjectionAwareInterface;
use Webird\Http\ServerSent\Event;
use Webird\Http\ServerSent\Exception;

/**
 *
 */
class ServerSent implements InjectionAwareInterface
{

    /**
     *
     */
    private $di;

    /**
     *
     */
    private $isStarted;

    /**
     *
     */
    public function __construct()
    {
        $this->isStarted = false;
    }

    /**
     *
     */
    public function setDI(DiInterface $di)
    {
        $this->di = $di;
    }

    /**
     *
     */
    public function getDI()
    {
        return $this->di;
    }

    /**
     *
     */
    public function start()
    {
        $response = $this->getDI()
            ->getResponse();
        $view = $this->getDI()
            ->getView();

        $view->disable();

        // Disable proxy buffering and fastcgi_buffering.
        $response->setHeader('X-Accel-Buffering', 'no');

        // Disable output compression.
        $response->setHeader('Content-Encoding', 'none');

        $response->setHeader('Cache-Control', 'no-cache');
        $response->setContentType('text/event-stream');
        $response->setContent('');
        $response->send();

        // Remove two levels of output buffering
        ob_get_clean();
        ob_get_clean();

        $this->isStarted = true;
    }

    /**
     *
     */
    public function setRetry($seconds, $flush = true)
    {
        if (!$this->isStarted) {
            throw new Exception('The ServerSent has not been started yet.');
        }

        $event = new Event();
        $event->setRetry($seconds);
        echo (string) $event;

        if ($flush) {
            flush();
        }

        return $this;
    }

    /**
     *
     */
    public function sendEvent(Event $event)
    {
        if (!$this->isStarted) {
            throw new Exception('The ServerSent has not been started yet.');
        }

        echo (string) $event;
        flush();

        return $this;
    }

    /**
     * Send a heartbeat comment to the browser.
     * This keeps the EventSource connection open in the browser
     * and allows PHP to know if the connection has ended.
     */
    public function sendHeartbeat()
    {
        if (!$this->isStarted) {
            throw new Exception('The ServerSent has not been started yet.');
        }

        echo ":heartbeat\n\n";
        flush();

        return $this;
    }

    /**
     *
     */
    public function end()
    {
        if (!$this->isStarted) {
            throw new Exception('The ServerSent is not running.');
        }

        // Rebuild the output buffering as we found it.
        ob_start();
        ob_start();
    }
}
