<?php
require __DIR__ . '/vendor/autoload.php';

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;

class Chat implements MessageComponentInterface
{
    private $clients = [];
    private $users = [];

    public function onOpen(ConnectionInterface $conn)
    {
        $query = $conn->httpRequest->getUri()->getQuery();
        parse_str($query, $params);

        $userId = $params['user_id'] ?? 'guest';
        $productId = $params['product_id'] ?? null;
        $sellerId = $params['seller_id'] ?? null;

        $this->clients[$conn->resourceId] = $conn;
        $this->users[$conn->resourceId] = [
            'user_id' => $userId,
            'product_id' => $productId,
            'seller_id' => $sellerId
        ];

        echo "New connection! ({$conn->resourceId}) - User ID: $userId\n";
    }

    public function onMessage(ConnectionInterface $from, $msg)
    {
        $data = json_decode($msg, true);

        $fromUser = $this->users[$from->resourceId];
        $userId = $fromUser['user_id'];
        $productId = $fromUser['product_id'];
        $sellerId = $fromUser['seller_id'];

        if ($data && isset($data['message']) && isset($data['target_user_id'])) {
            foreach ($this->clients as $rid => $client) {
                if ($client === $from) continue;

                $target = $this->users[$rid];
                if (
                    $target['product_id'] == $productId &&
                    ($target['user_id'] == $sellerId || $target['user_id'] == $data['target_user_id'])
                ) {
                    $client->send(json_encode([
                        'user_id' => $userId,
                        'message' => $data['message'],
                        'timestamp' => date('Y-m-d H:i:s')
                    ]));
                }
            }
        }
    }

    public function onClose(ConnectionInterface $conn)
    {
        unset($this->clients[$conn->resourceId], $this->users[$conn->resourceId]);
        echo "Connection {$conn->resourceId} disconnected.\n";
    }

    public function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "Error: {$e->getMessage()}\n";
        $conn->close();
    }
}

$server = IoServer::factory(
    new HttpServer(
        new WsServer(
            new Chat()
        )
    ),
    9000 // đổi từ 8080 sang 9000
);
$server->run();
