<?php
namespace Webird\Http;

use Phalcon\DiInterface;
use Webird\Http\ServerSent\Event;

/**
 *
 */
class ServerSent
{

    /**
     *
     */
    private $di;

    /**
     *
     */
    private $eventName;

    /**
     *
     */
    public function __construct(DiInterface $di)
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

        $response->setheader('Cache-Control', 'no-cache');
        $response->setContentType('text/event-stream');
        $response->setContent('');
        $response->send();

        // Remove two levels of output buffering
        ob_get_clean();
        ob_get_clean();
    }

    /**
     *
     */
    public function sendEvent(Event $event)
    {
        echo (string) $event;
        flush();
    }

    /**
     * Send a heartbeat comment to the browser.
     * This keeps the EventSource connection open in the browser and allows PHP to know if
     * the connection has ended.
     */
    public function sendHeartbeat()
    {
        echo ":heartbeat\n\n";
        flush();
    }

    /**
     *
     */
    public function end()
    {
        // Rebuild the output buffering as we found it.
        ob_start();
        ob_start();
    }
}
