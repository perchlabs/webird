<?php
namespace Webird\Logger\Adapter;

use Phalcon\Logger\Adapter;
use Phalcon\Logger\AdapterInterface;
use Phalcon\Logger\Formatter\Line as Formatter;

/**
 * Webird\Logger\Adapter\Error
 * Sends messages to PHP error log
 *
 */
class Error extends Adapter implements AdapterInterface
{

    /**
     * {@inheritdoc}
     *
     * @return \Phalcon\Logger\Formatter\Line
     */
    public function getFormatter()
    {
        if (!$this->_formatter) {
            $this->_formatter = new Formatter('%message%');
        }

        return $this->_formatter;
    }

    /**
     */
    public function logInternal($message, $type, $time, $context = [])
    {
        $entry = $this->getFormatter()->format($message, $type, $time, $context);
        error_log($entry);
    }

    /**
     * {@inheritdoc}
     *
     * @return boolean
     */
    public function close()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function begin()
    {
    }

    /**
     * {@inheritdoc}
     * Encodes all collected messages into HTTP headers. This method is registered as a shutdown handler,
     * so transactions will get committed even if you forget to commit them yourself.
     */
    public function commit()
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function flush()
    {
    }
}
