<?php

namespace App\Models;

use App\Config\Database;

class ChatModel
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getChats($productId, $userId, $sellerId)
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM chats WHERE product_id = ? AND ((sender_id = ? AND receiver_id = ?) OR (sender_id = ? AND receiver_id = ?)) ORDER BY created_at ASC");
            $stmt->execute([$productId, $userId, $sellerId, $sellerId, $userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Lỗi khi lấy lịch sử chat: " . $e->getMessage());
        }
    }

    public function getChatHistoryForUser($viewerId, $participantId, $productId)
    {
        try {
            $stmt = $this->db->prepare("SELECT c.*, u.username AS sender_name, u2.username AS receiver_name 
                                        FROM chats c 
                                        LEFT JOIN users u ON c.sender_id = u.id 
                                        LEFT JOIN users u2 ON c.receiver_id = u2.id 
                                        WHERE c.product_id = ? AND 
                                              ((c.sender_id = ? AND c.receiver_id = ?) OR 
                                               (c.sender_id = ? AND c.receiver_id = ?)) 
                                        ORDER BY c.created_at ASC");
            $stmt->execute([$productId, $viewerId, $participantId, $participantId, $viewerId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Lỗi khi lấy lịch sử chat cho người dùng: " . $e->getMessage());
        }
    }

    public function getUserConversations($userId)
    {
        try {
            $stmt = $this->db->prepare("SELECT DISTINCT 
                    CASE 
                        WHEN c.sender_id = ? THEN c.receiver_id 
                        ELSE c.sender_id 
                    END AS other_user_id,
                    u.username AS other_user_name,
                    c.product_id,
                    MAX(c.created_at) AS last_message_time
                FROM chats c
                LEFT JOIN users u ON u.id = CASE 
                    WHEN c.sender_id = ? THEN c.receiver_id 
                    ELSE c.sender_id 
                END
                WHERE c.sender_id = ? OR c.receiver_id = ?
                GROUP BY 
                    CASE 
                        WHEN c.sender_id = ? THEN c.receiver_id 
                        ELSE c.sender_id 
                    END,
                    u.username,
                    c.product_id
                ORDER BY last_message_time DESC");
            $stmt->execute([$userId, $userId, $userId, $userId, $userId]);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception("Lỗi khi lấy danh sách cuộc trò chuyện: " . $e->getMessage());
        }
    }

    public function saveChat($senderId, $receiverId, $productId, $message)
    {
        try {
            $stmt = $this->db->prepare("INSERT INTO chats (sender_id, receiver_id, product_id, message) VALUES (?, ?, ?, ?)");
            $success = $stmt->execute([$senderId, $receiverId, $productId, $message]);
            if ($success) {
                return [
                    'success' => true,
                    'timestamp' => date('Y-m-d H:i:s')
                ];
            }
            return ['success' => false, 'message' => 'Không thể lưu tin nhắn vào cơ sở dữ liệu'];
        } catch (\PDOException $e) {
            error_log("Lỗi khi lưu tin nhắn: " . $e->getMessage());
            return ['success' => false, 'message' => 'Lỗi cơ sở dữ liệu: ' . $e->getMessage()];
        }
    }
}
