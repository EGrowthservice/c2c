<?php
namespace App\Controllers;

use App\Models\Report;
use App\Models\Product;
use App\Helpers\Session;
use App\WebSocket\NotificationServer;

class ReportController
{
    private $reportModel;
    private $productModel;

    public function __construct()
    {
        $this->reportModel = new Report();
        $this->productModel = new Product();
    }

    public function create($productId)
    {
        if (!Session::get('user')) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập để báo cáo người dùng!']);
                exit;
            }
            Session::set('error', 'Vui lòng đăng nhập để báo cáo người dùng!');
            header('Location: /login');
            exit;
        }

        $product = $this->productModel->getProductById($productId);
        if (!$product) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Sản phẩm không tồn tại!']);
                exit;
            }
            Session::set('error', 'Sản phẩm không tồn tại!');
            header('Location: /products');
            exit;
        }
        $reportedUserId = $product['user_id'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $reason = $_POST['reason'] ?? '';

            if (empty($reason)) {
                $response = ['success' => false, 'message' => 'Vui lòng nhập lý do báo cáo!'];
            } else {
                if ($this->reportModel->create($reportedUserId, $reason)) {
                    $response = ['success' => true, 'message' => 'Báo cáo người dùng thành công!'];
                    // Gửi thông báo cho admin (giả sử admin có user_id = 1)
                    NotificationServer::sendNotification(
                        1, // Admin user_id
                        'report',
                        [
                            'product_name' => $product['title'],
                            'reason' => $reason,
                            'reporter_name' => Session::get('user')['name'] ?? 'Người dùng',
                            'timestamp' => date('Y-m-d H:i:s'),
                            'link' => "/admin/reports/{$productId}"
                        ]
                    );
                } else {
                    $response = ['success' => false, 'message' => 'Báo cáo người dùng thất bại!'];
                }
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }

            if ($response['success']) {
                Session::set('success', $response['message']);
            } else {
                Session::set('error', $response['message']);
            }
            header('Location: /products/' . $productId);
            exit;
        }

        require_once __DIR__ . '/../Views/report/create.php';
    }
}