<?php

namespace App\Models;

use App\Config\Database;

class Favorite
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function add($userId, $productId)
    {
        $stmt = $this->db->prepare("SELECT id FROM products WHERE id = ? AND status = 'approved'");
        $stmt->execute([$productId]);
        if (!$stmt->fetch()) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT id FROM favorites WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        if ($stmt->fetch()) {
            return false;
        }

        $stmt = $this->db->prepare("INSERT INTO favorites (user_id, product_id) VALUES (?, ?)");
        return $stmt->execute([$userId, $productId]);
    }

    public function remove($userId, $productId)
    {
        $stmt = $this->db->prepare("DELETE FROM favorites WHERE user_id = ? AND product_id = ?");
        return $stmt->execute([$userId, $productId]);
    }

    public function getByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT f.*, p.title, p.price, p.image 
            FROM favorites f 
            JOIN products p ON f.product_id = p.id 
            WHERE f.user_id = ?
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
