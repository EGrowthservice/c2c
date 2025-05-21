<?php

namespace App\Models;

use App\Config\Database;

class Order
{
    private $db;
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
    }

    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT o.*, p.title, p.image, u.username as seller_name 
                                    FROM orders o 
                                    JOIN products p ON o.product_id = p.id 
                                    JOIN users u ON o.seller_id = u.id 
                                    WHERE o.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
    public function getBySeller($sellerId)
    {
        $stmt = $this->db->prepare("SELECT o.*, p.title, p.image, u.username as buyer_name 
                                FROM orders o 
                                JOIN products p ON o.product_id = p.id 
                                JOIN users u ON o.buyer_id = u.id 
                                WHERE o.seller_id = ? 
                                ORDER BY o.created_at DESC");
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getOrdersBySellerId($sellerId)
    {
        $query = "SELECT o.*, p.title, p.image, u.username as buyer_name 
                  FROM orders o 
                  JOIN products p ON o.product_id = p.id 
                  JOIN users u ON o.buyer_id = u.id 
                  WHERE o.seller_id = ? 
                  ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$sellerId]);
        return $stmt->fetchAll();
    }

    public function getOrdersByBuyerId($buyerId)
    {
        $query = "SELECT o.*, p.title, p.image, u.username as seller_name 
                  FROM orders o 
                  JOIN products p ON o.product_id = p.id 
                  JOIN users u ON o.seller_id = u.id 
                  WHERE o.buyer_id = ? 
                  ORDER BY o.created_at DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$buyerId]);
        return $stmt->fetchAll();
    }

    public function getOrderById($id)
    {
        $query = "SELECT * FROM orders WHERE id = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }


    public function create($buyerId, $sellerId, $productId, $quantity, $totalPrice)
    {
        $stmt = $this->db->prepare("
            INSERT INTO orders (buyer_id, seller_id, product_id, quantity, total_price, status)
            VALUES (?, ?, ?, ?, ?, 'pending')
        ");
        $stmt->execute([$buyerId, $sellerId, $productId, $quantity, $totalPrice]);
        return $this->db->lastInsertId();
    }

    public function addDetail($orderId, $details)
    {
        $stmt = $this->db->prepare("
            INSERT INTO order_detail (order_id, fullname, phone, pincode, state, town_city, house_no, road_name, landmark)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $orderId,
            $details['fullname'],
            $details['phone'],
            $details['pincode'],
            $details['state'],
            $details['town_city'],
            $details['house_no'],
            $details['road_name'],
            $details['landmark']
        ]);
    }

    public function createPayment($orderId, $paymentMethod, $amount, $transactionId = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO payment (order_id, payment_method, transaction_id, amount, status)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([$orderId, $paymentMethod, $transactionId, $amount, 'pending']);
    }

    public function updatePaymentStatus($orderId, $status, $transactionId = null)
    {
        $stmt = $this->db->prepare("UPDATE payment SET status = ?, transaction_id = ? WHERE order_id = ?");
        return $stmt->execute([$status, $transactionId, $orderId]);
    }

    public function updateStatus($orderId, $status, $trackingNumber = null, $carrier = null)
    {
        $stmt = $this->db->prepare("
            UPDATE orders 
            SET status = ?, tracking_number = ?, carrier = ?, updated_at = CURRENT_TIMESTAMP 
            WHERE id = ?
        ");
        return $stmt->execute([$status, $trackingNumber, $carrier, $orderId]);
    }
}
