<?php
require dirname(__DIR__) . '/chat_app/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class Chat implements MessageComponentInterface {
    protected $clients;

    public function __construct() {
        $this->clients = new \SplObjectStorage;
    }

    public function onOpen(ConnectionInterface $conn) {
        $this->clients->attach($conn);
        echo "New connection: {$conn->resourceId}\n";
    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = json_decode($msg, true);

        if (isset($data['room_id'], $data['user_id'], $data['message'])) {
            $room_id = $data['room_id'];
            $user_id = $data['user_id'];
            $message_text = $data['message'];
            $username = "Unknown";

            // Connect to database
            $connDb = new mysqli("localhost", "root", "", "chat_app");
            if (!$connDb->connect_error) {
                // Get username
                $stmt = $connDb->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->bind_result($username);
                $stmt->fetch();
                $stmt->close();

                // Save message
                $insert = $connDb->prepare("INSERT INTO messages (room_id, user_id, message_text) VALUES (?, ?, ?)");
                $insert->bind_param("iis", $room_id, $user_id, $message_text);
                $insert->execute();
                $insert->close();

                $connDb->close();
            }

            $formatted = $username . ": " . $message_text;

            // Broadcast to all clients
            foreach ($this->clients as $client) {
                $client->send($formatted);
            }

            echo "Message from $username: $message_text\n";
        }
    }

    public function onClose(ConnectionInterface $conn) {
        $this->clients->detach($conn);
        echo "Connection {$conn->resourceId} has disconnected\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        echo "An error occurred: {$e->getMessage()}\n";
        $conn->close();
    }
}

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    8080
);

$server->run();
