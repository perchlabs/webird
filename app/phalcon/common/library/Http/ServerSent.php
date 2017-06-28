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
    private $isRunning;

    /**
     *
     */
    private $keepAlive;

    /**
     *
     */
    private $retryDelay;

    /**
     *
     */
    private $lastTime;

    /**
     *
     */
    public function __construct(array $options = [])
    {
        if (array_key_exists('keepAlive', $options)) {
            $keepAlive = $options['keepAlive'];
            if (!is_int($keepAlive) && !is_float($keepAlive)) {
                throw new Exception('keepAlive value must be a number.');
            }
            if ($keepAlive <= 0) {
                throw new Exception('keepAlive must be great than 0.');
            }
            $this->keepAlive = $keepAlive;
        }

        if (array_key_exists('retryDelay', $options)) {
            $retryDelay = $options['retryDelay'];
            if (!is_int($retryDelay)) {
                throw new Exception('retryDelay value must be an integer.');
            }
            $this->retryDelay = $retryDelay;
        }

        $this->isRunning = false;
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

        $content = '';
        if ($this->retryDelay) {
            $content .= 'retry:' . $this->retryDelay . "\n\n";
        }

        $response->setContent($content);
        $response->send();

        // Remove two levels of output buffering
        ob_get_clean();
        ob_get_clean();

        $this->lastTime = microtime(true);

        $this->isRunning = true;
    }

    /**
     *
     */
    public function sendEvent(Event $event)
    {
        $text = (string) $event;
        $this->flushText($text);

        return $this;
    }

    /**
     *
     */
    public function keepAlive()
    {
        $now = microtime(true);
        if ($this->keepAlive && $now - $this->lastTime > $this->keepAlive) {
            $this->sendHeartbeat();
        }

        return $this;
    }

    /**
     * Send a heartbeat comment to the browser.
     * This keeps the EventSource connection open in the browser
     * and allows PHP to know if the connection has ended.
     */
    public function sendHeartbeat()
    {
        $this->flushText(":heartbeat\n\n");

        return $this;
    }

    /**
     *
     */
    public function end()
    {
        if (!$this->isRunning) {
            throw new Exception('The ServerSent is not running.');
        }

        // Rebuild the output buffering as we found it.
        ob_start();
        ob_start();

        $this->isRunning = false;
    }

    /**
     *
     */
    protected function flushText($text)
    {
        if (!$this->isRunning) {
            throw new Exception('The ServerSent is not running.');
        }

        echo $text;
        flush();

        $this->lastTime = microtime(true);
    }
}
