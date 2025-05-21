<?php
namespace App\Models;

use App\Config\Database;

class Review {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function create($productId, $userId, $rating, $comment) {
        $stmt = $this->db->prepare("INSERT INTO reviews (product_id, user_id, rating, comment) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$productId, $userId, $rating, $comment]);
    }

    public function getByProduct($productId) {
        $stmt = $this->db->prepare("SELECT r.*, u.username FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.created_at DESC");
        $stmt->execute([$productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}