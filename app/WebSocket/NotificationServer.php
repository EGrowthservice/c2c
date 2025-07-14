<?php
namespace App\WebSocket;

use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;

class NotificationServer implements MessageComponentInterface
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
        if (!$data || !isset($data['type'])) {
            return;
        }

        $fromUser = $this->users[$from->resourceId];
        $userId = $fromUser['user_id'];
        $productId = $fromUser['product_id'];
        $sellerId = $fromUser['seller_id'];

        foreach ($this->clients as $rid => $client) {
            if ($client === $from) {
                continue;
            }

            $target = $this->users[$rid];
            $targetUserId = $data['target_user_id'] ?? null;

            if ($data['type'] === 'message' && $target['product_id'] == $productId &&
                ($target['user_id'] == $sellerId || $target['user_id'] == $targetUserId)) {
                $client->send(json_encode([
                    'type' => 'message',
                    'user_id' => $userId,
                    'message' => $data['message'],
                    'timestamp' => $data['timestamp'],
                    'sender_name' => $data['sender_name'] ?? 'Người dùng',
                    'link' => $data['link'] ?? '#'
                ]));
            } elseif (in_array($data['type'], ['order', 'review', 'report']) && $target['user_id'] == $targetUserId) {
                $client->send(json_encode($data));
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

    public static function sendNotification($targetUserId, $type, $data)
    {
        global $notificationServer;
        if (!isset($notificationServer->clients)) {
            return false;
        }

        $message = json_encode(array_merge(['type' => $type, 'target_user_id' => $targetUserId], $data));
        foreach ($notificationServer->clients as $rid => $client) {
            $clientInfo = $notificationServer->users[$rid] ?? [];
            if ($clientInfo['user_id'] == $targetUserId) {
                $client->send($message);
            }
        }
        return true;
    }
}