<?php
namespace App\Controllers;

use App\Models\ChatModel;
use App\Helpers\Session;
use Textalk\WebSocket\Client; // Đảm bảo dòng này có

class ChatController
{
    private $chatModel;

    public function __construct()
    {
        $this->chatModel = new ChatModel();
    }

    public function GetChat($product_id, $seller_id)
    {
        $currentUserId = Session::get('user')['id'] ?? null;

        if (!$currentUserId || !$product_id || !$seller_id) {
            Session::set('error', 'Vui lòng đăng nhập và kiểm tra thông tin sản phẩm!');
            header('Location: /login');
            exit;
        }

        require_once __DIR__ . '/../Views/chat/chat.php';
    }

    public function GetConversations()
    {
        $currentUserId = Session::get('user')['id'] ?? null;

        if (!$currentUserId) {
            Session::set('error', 'Vui lòng đăng nhập để xem danh sách cuộc trò chuyện!');
            header('Location: /login');
            exit;
        }

        $conversations = $this->chatModel->getUserConversations($currentUserId);
        require_once __DIR__ . '/../Views/chat/conversations.php';
    }

    public function save()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $senderId = $_POST['sender_id'] ?? null;
            $receiverId = $_POST['receiver_id'] ?? null;
            $productId = $_POST['product_id'] ?? null;
            $message = trim($_POST['message'] ?? '');

            if ($senderId && $receiverId && $productId && $message) {
                $result = $this->chatModel->saveChat($senderId, $receiverId, $productId, $message);
                if ($result['success']) {
                    $messageData = [
                        'type' => 'message',
                        'target_user_id' => $receiverId,
                        'sender_name' => Session::get('user')['name'] ?? 'Người dùng',
                        'message' => $message,
                        'timestamp' => date('Y-m-d H:i:s'),
                        'link' => "/chat?product_id={$productId}&seller_id={$senderId}"
                    ];
                    try {
                        $client = new Client('ws://localhost:9000'); // Dòng 66
                        $client->send(json_encode($messageData));
                        $client->close();
                    } catch (\Exception $e) {
                        error_log("Lỗi gửi WebSocket: " . $e->getMessage());
                    }
                }
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ']);
        }
    }
}