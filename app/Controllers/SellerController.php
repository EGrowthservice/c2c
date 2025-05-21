<?php

namespace App\Controllers;

use App\Models\SellerRating;
use App\Models\Product;
use App\Helpers\Session;

class SellerController
{
    private $ratingModel;
    private $productModel;
    public function __construct()
    {
        $this->ratingModel = new SellerRating();
        $this->productModel = new Product();
    }
    public function show($sellerId)
    {
        $ratings = $this->ratingModel->getBySeller($sellerId);
        $products = $this->productModel->getAll(); // Lọc sản phẩm của seller nếu cần
        $seller = $this->db->query("SELECT username, average_rating, rating_count FROM users WHERE id = ?", [$sellerId])->fetch(\PDO::FETCH_ASSOC);
        require_once __DIR__ . '/../Views/seller/show.php';
    }
    public function rate($sellerId)
    {
        if (!Session::get('user')) {
            Session::set('error', 'Vui lòng đăng nhập để đánh giá!');
            header('Location: /login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $buyerId = Session::get('user')['id'];
            $rating = $_POST['rating'];
            $comment = $_POST['comment'];
            if ($this->ratingModel->create($sellerId, $buyerId, $rating, $comment)) {
                Session::set('success', 'Đánh giá người bán thành công!');
            } else {
                Session::set('error', 'Đánh giá thất bại!');
            }
            header('Location: /sellers/' . $sellerId);
            exit;
        }
        require_once __DIR__ . '/../Views/seller/rate.php';
    }
}
