<?php
namespace Webird\Cli;

use Phalcon\DI\Injectable as DIInjectable,
    Ratchet\MessageComponentInterface,
    Ratchet\ConnectionInterface,
    Webird\DatabaseSessionReader;


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
        try {
            echo "New connection! ({$conn->resourceId})\n";

            // TODO: This authentication code could easily be brought into a generic class

            $cookies = $conn->WebSocket->request->getCookies();
            if (! array_key_exists('PHPSESSID', $cookies)) {
                echo "Connection Rejected: Session Cookie was not present.\n";
                return $conn->close();
            }
            $sessionId = $cookies['PHPSESSID'];

            $sessionReader = $this->getDI()->getSessionReader();
            if ($sessionReader->read($sessionId) === false) {
                echo "Connection Rejected: Session could not be found.\n";
                return $conn->close();
            }
            if (($identity = $sessionReader->get('auth-identity')) === false) {
                echo "Connection Rejected: session auth-identity data is not present.\n";
                return $conn->close();
            }
            if (!isset($identity['role'])) {
                echo "Connection Rejected: session user role data is not present.\n";
                return $conn->close();
            }
            $role = $identity['role'];

            $acl = $this->getDI()->getAcl();
            if (!$this->acl->isAllowed($role, 'cli:chat', 'open')) {
                echo "Connection Rejected: user does not have permission to open a websocket.\n";
                return $conn->close();
            }

        // Store the new connection to send messages to later
        $this->clients->attach($conn, $sessionId);

        } catch (\Exception $e) {
            $conn->close();
            echo $e->getMessage() . "\n";
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

        echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
            , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');

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

        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    /**
     * Handles exceptions in the application
     *
     * @param \Ratchet\ConnectionInterface  $from
     * @param \Exception                    $e
    */
    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error has occurred: {$e->getMessage()}\n";

        $conn->close();
    }

}
