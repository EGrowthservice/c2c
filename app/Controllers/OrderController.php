<?php
namespace App\Controllers;

use App\Models\Order;
use App\Helpers\Session;
use App\WebSocket\NotificationServer;

class OrderController
{
    private $orderModel;

    public function __construct()
    {
        $this->orderModel = new Order();
    }

    public function track($id)
    {
        if (!Session::get('user')) {
            header('Location: /login');
            exit;
        }

        $order = $this->orderModel->find($id);

        if (!$order || $order['buyer_id'] != Session::get('user')['id']) {
            Session::set('error', 'Đơn hàng không tồn tại hoặc không thuộc về bạn!');
            header('Location: /orders');
            exit;
        }
        require_once __DIR__ . '/../Views/order/track.php';
    }

    public function updateOrder($id)
    {
        if (!Session::get('user')) {
            header('Location: /login');
            exit;
        }
        $order = $this->orderModel->find($id);
        if (!$order || $order['seller_id'] != Session::get('user')['id']) {
            Session::set('error', 'Đơn hàng không tồn tại hoặc không thuộc về bạn!');
            header('Location: /profile/orders');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'];
            $trackingNumber = $_POST['tracking_number'] ?? null;
            $carrier = $_POST['carrier'] ?? null;
            if ($this->orderModel->updateStatus($id, $status, $trackingNumber, $carrier)) {
                Session::set('success', 'Cập nhật đơn hàng thành công!');
                // Gửi thông báo cho người mua
                NotificationServer::sendNotification(
                    $order['buyer_id'],
                    'order',
                    [
                        'order_id' => $id,
                        'status' => $status,
                        'timestamp' => date('Y-m-d H:i:s'),
                        'link' => "/orders/{$id}"
                    ]
                );
            } else {
                Session::set('error', 'Cập nhật thất bại!');
            }
            header('Location: /profile/orders');
            exit;
        }
        require_once __DIR__ . '/../Views/order/update.php';
    }

    public function cancel($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['_token'] === Session::get('csrf_token')) {
            $order = $this->orderModel->getOrderById($id);
            if (!$order || $order['buyer_id'] !== Session::get('user')['id']) {
                $response = ['success' => false, 'message' => 'Đơn hàng không tồn tại hoặc bạn không có quyền hủy!'];
            } elseif ($order['status'] !== 'pending') {
                $response = ['success' => false, 'message' => 'Chỉ có thể hủy đơn hàng ở trạng thái chờ xử lý!'];
            } elseif ($this->orderModel->updateStatus($id, 'cancelled')) {
                $response = ['success' => true, 'message' => 'Hủy đơn hàng thành công!'];
                // Gửi thông báo cho người bán
                NotificationServer::sendNotification(
                    $order['seller_id'],
                    'order',
                    [
                        'order_id' => $id,
                        'status' => 'cancelled',
                        'timestamp' => date('Y-m-d H:i:s'),
                        'link' => "/profile/orders/{$id}"
                    ]
                );
            } else {
                $response = ['success' => false, 'message' => 'Hủy đơn hàng thất bại!'];
            }
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        Session::set('error', 'Yêu cầu không hợp lệ!');
        header('Location: /profile/my-orders');
        exit;
    }
}