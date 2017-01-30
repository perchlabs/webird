<?php
namespace Webird\Modules\Cli;

use Phalcon\DI\Injectable as DIInjectable;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Webird\DatabaseSessionReader;

/**
 * Basic chat logic for a Ratchet application
 */
class Chat extends DIInjectable implements MessageComponentInterface
{

    protected $clients;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->clients = new \SplObjectStorage();
    }

    /**
     * Connection open function
     *
     * @param \Ratchet\ConnectionInterface  $conn
     */
    public function onOpen(ConnectionInterface $conn)
    {
        $sessionReader = $this->getDI()
            ->getSessionReader();
        $acl = $this->getDI()
            ->getAcl();

        try {
            fwrite(STDOUT, "New connection! ({$conn->resourceId})\n");

            // TODO: This authentication code could easily be brought into a generic class
            $cookies = $conn->WebSocket->request->getCookies();
            if (!array_key_exists('PHPSESSID', $cookies)) {
                fwrite(STDERR, "Connection Rejected: Session Cookie was not present.\n");
                return $conn->close();
            }
            $sessionId = $cookies['PHPSESSID'];

            if ($sessionReader->read($sessionId) === false) {
                fwrite(STDERR, "Connection Rejected: Session could not be found.\n");
                return $conn->close();
            }
            if (($identity = $sessionReader->get('auth-identity')) === false) {
                fwrite(STDERR, "Connection Rejected: session auth-identity data is not present.\n");
                return $conn->close();
            }

            if (!isset($identity['role'])) {
                fwrite(STDERR, "Connection Rejected: session user role data is not present.\n");
                return $conn->close();
            }
            $role = $identity['role'];

            if (!$acl->isAllowed($role, 'cli::chat', 'open')) {
                fwrite(STDERR, "Connection Rejected: user does not have permission to open a websocket.\n");
                return $conn->close();
            }

        // Store the new connection to send messages to later
        $this->clients->attach($conn, $sessionId);

        } catch (\Exception $e) {
            $conn->close();
            fwrite(STDOUT, $e->getMessage() . "\n");
        }
    }

    /**
     * Receives a message when registered in the websocket server
     *
     * @param \Ratchet\ConnectionInterface  $from
     * @param string                        $msg
    */
    public function onMessage(ConnectionInterface $from, $msg) {
        $numRecv = $this->clients->count() - 1;

        fwrite(STDOUT, sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's'));

        foreach ($this->clients as $client) {
            if ($from !== $client) {
                // The sender is not the receiver, send to each client connected
                $client->send($msg);
            }
        }
    }

    /**
     * Handle closing of a connection
     *
     * @param \Ratchet\ConnectionInterface  $conn
    */
    public function onClose(ConnectionInterface $conn) {
        // The connection is closed, remove it, as we can no longer send it messages
        $this->clients->detach($conn);

        fwrite(STDOUT, "Connection {$conn->resourceId} has disconnected\n");
    }

    /**
     * Handles exceptions in the application
     *
     * @param \Ratchet\ConnectionInterface  $from
     * @param \Exception                    $e
    */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        fwrite(STDERR, $e->getMessage() . "\n");

        $conn->close();
    }

}
