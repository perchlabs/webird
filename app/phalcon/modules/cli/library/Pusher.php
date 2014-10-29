<?php
namespace Webird;

// use Ratchet\ConnectionInterface,
//     Ratchet\Wamp\WampServerInterface;
//
// /**
//  * Application logic for WAMP application
//  */
// class Pusher implements WampServerInterface
// {
//     protected $clients;
//     protected $subscribedTopics;
//
//     /**
//      * Class constructor
//      */
//     public function __construct()
//     {
//         $this->clients = new \SplObjectStorage();
//         $this->subscribedTopics = [];
//     }
//
//     /**
//      * Connection open function
//      *
//      * @param \Ratchet\ConnectionInterface  $conn
//      */
//     public function onOpen(ConnectionInterface $conn)
//     {
//         // Store the new connection to send messages to later
//         $this->clients->attach($conn);
//
//         echo "New connection! ({$conn->resourceId})\n";
//     }
//
//     /**
//      * Receives a message when registered in the WAMP server
//      *
//      * @param \Ratchet\ConnectionInterface  $from
//      * @param string                        $msg
//     */
//     public function onMessage(ConnectionInterface $from, $msg)
//     {
//         $numRecv = count($this->clients) - 1;
//         echo sprintf('Connection %d sending message "%s" to %d other connection%s' . "\n"
//             , $from->resourceId, $msg, $numRecv, $numRecv == 1 ? '' : 's');
//
//         foreach ($this->clients as $client) {
//             if ($from !== $client) {
//                 // The sender is not the receiver, send to each client connected
//                 $client->send($msg);
//             }
//         }
//     }
//
//     /**
//      * Handle closing of a connection
//      *
//      * @param \Ratchet\ConnectionInterface  $conn
//     */
//     public function onClose(ConnectionInterface $conn)
//     {
//         // The connection is closed, remove it, as we can no longer send it messages
//         $this->clients->detach($conn);
//
//         echo "Connection {$conn->resourceId} has disconnected\n";
//     }
//
//     /**
//      * Handles exceptions in the application
//      *
//      * @param \Ratchet\ConnectionInterface  $from
//      * @param \Exception                    $e
//     */
//     public function onError(ConnectionInterface $conn, \Exception $e)
//     {
//         echo "An error has occurred: {$e->getMessage()}\n";
//
//         $conn->close();
//     }
//
//
//
//
//
//
//
//     public function onSubscribe(ConnectionInterface $conn, $topic)
//     {
//         error_log("{$topic->getId()}, $topic");
//         $this->subscribedTopics[$topic->getId()] = $topic;
//     }
//
//     // public function onUnsubscribe(ConnectionInterface $conn, Topic $topic) {
//     public function onUnsubscribe(ConnectionInterface $conn, $topic)
//     {
//
//     }
//
//
//     //onPublish(Ratchet\ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible)
//
//     // public function onPublish(ConnectionInterface $conn, Topic $topic, string $event) {
//     public function onPublish(ConnectionInterface $conn, $topic, $event, array $exclude, array $eligible) {
//
//     }
//
//
//     // public function onCall (ConnectionInterface $conn, string $id, Topic $topic, array $params) {
//     public function onCall (ConnectionInterface $conn, $id, $topic, array $params) {
//
//     }
//
//
//
//     /**
//      * @param string JSON'ified string we'll receive from ZeroMQ
//      */
//     public function onBlogEntry($entry)
//     {
//         $entryData = json_decode($entry, true);
//         error_log(var_export($entry, true));
//
//         // If the lookup topic object isn't set there is no one to publish to
//         if (!array_key_exists($entryData['category'], $this->subscribedTopics)) {
//             return;
//         }
//
//         $topic = $this->subscribedTopics[$entryData['category']];
//
//         // re-send the data to all the clients subscribed to that category
//         $topic->broadcast($entryData);
//     }
//
//     /* The rest of our methods were as they were, omitted from docs to save space */
// }
