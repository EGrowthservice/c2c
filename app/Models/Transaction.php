<?php
namespace App\Models;

use App\Config\Database;

class Transaction {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }

    public function create($orderId, $amount, $paymentMethod, $quantity) {
        $stmt = $this->db->prepare("INSERT INTO transactions (order_id, amount, payment_method, quantity) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$orderId, $amount, $paymentMethod, $quantity]);
    }

    public function getAll() {
        $stmt = $this->db->query("SELECT t.*, o.buyer_id, p.title FROM transactions t JOIN orders o ON t.order_id = o.id JOIN products p ON o.product_id = p.id");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}