<?php

namespace App\Models;

use App\Config\Database;

class Admin
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function getStats()
    {
        $stats = [];
        $stats['products'] = $this->db->query("SELECT COUNT(*) as count FROM products")->fetch(\PDO::FETCH_ASSOC)['count'];
        $stats['users'] = $this->db->query("SELECT COUNT(*) as count FROM users")->fetch(\PDO::FETCH_ASSOC)['count'];
        $stats['orders'] = $this->db->query("SELECT COUNT(*) as count FROM orders")->fetch(\PDO::FETCH_ASSOC)['count'];
        $stats['revenue'] = $this->db->query("SELECT SUM(amount) as total FROM transactions WHERE status = 'completed'")->fetch(\PDO::FETCH_ASSOC)['total'] ?? 0;
        return $stats;
    }

    public function searchProducts($keyword)
    {
        $keyword = "%$keyword%";
        $stmt = $this->db->prepare("SELECT * FROM products WHERE title LIKE ? OR description LIKE ?");
        $stmt->execute([$keyword, $keyword]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function searchUsers($keyword)
    {
        $keyword = "%$keyword%";
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username LIKE ? OR email LIKE ?");
        $stmt->execute([$keyword, $keyword]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getAllProducts()
    {
        $stmt = $this->db->query("SELECT * FROM products");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function updateProductStatus($id, $status)
    {
        if (!in_array($status, ['pending', 'approved', 'rejected'])) {
            return false;
        }
        $stmt = $this->db->prepare("UPDATE products SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }

    public function getAllUsers()
    {
        $stmt = $this->db->query("SELECT * FROM users");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function toggleUserStatus($id, $is_active)
    {
        $stmt = $this->db->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        return $stmt->execute([$is_active, $id]);
    }

    public function getAllReports()
    {
        $stmt = $this->db->query("SELECT r.*, p.title, u.username FROM reports r JOIN products p ON r.product_id = p.id JOIN users u ON r.user_id = u.id");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleteReport($id)
    {
        $stmt = $this->db->prepare("DELETE FROM reports WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
