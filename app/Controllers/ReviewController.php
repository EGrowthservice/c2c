<?php
namespace App\Controllers;

use App\Models\Review;
use App\Models\Product;
use App\Helpers\Session;
use App\WebSocket\NotificationServer;

class ReviewController
{
    private $reviewModel;
    private $productModel;

    public function __construct()
    {
        $this->reviewModel = new Review();
        $this->productModel = new Product();
    }

    public function create($productId)
    {
        if (!Session::get('user')) {
            header('Location: /login');
            exit;
        }
        $product = $this->productModel->find($productId);
        if (!$product) {
            Session::set('error', 'Sản phẩm không tồn tại!');
            header('Location: /products');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = Session::get('user')['id'];
            $rating = $_POST['rating'];
            $comment = $_POST['comment'];

            if ($this->reviewModel->create($productId, $userId, $rating, $comment)) {
                Session::set('success', 'Đánh giá thành công!');
                // Gửi thông báo cho người bán
                NotificationServer::sendNotification(
                    $product['user_id'],
                    'review',
                    [
                        'product_name' => $product['title'],
                        'rating' => $rating,
                        'reviewer_name' => Session::get('user')['name'] ?? 'Người dùng',
                        'timestamp' => date('Y-m-d H:i:s'),
                        'link' => "/products/{$productId}/reviews"
                    ]
                );
                header('Location: /products/' . $productId);
                exit;
            } else {
                Session::set('error', 'Đánh giá thất bại!');
            }
        }
        require_once __DIR__ . '/../Views/review/create.php';
    }
}