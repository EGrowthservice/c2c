<?php

namespace App\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Helpers\Session;
use App\Config\Database;

class CheckoutController
{
    private $cartModel;
    private $orderModel;
    private $db;

    public function __construct()
    {
        $this->cartModel = new Cart();
        $this->orderModel = new Order();
        $this->db = (new Database())->getConnection();
    }

    public function index()
    {
        if (!Session::get('user')) {
            header('Location: /login');
            exit;
        }
        $cartItems = $this->cartModel->getByUser(Session::get('user')['id']);
        if (empty($cartItems)) {
            Session::set('error', 'Giỏ hàng trống!');
            header('Location: /cart');
            exit;
        }
        require_once __DIR__ . '/../Views/checkout/index.php';
    }

    public function process()
    {
        if (!Session::get('user')) {
            header('Location: /login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            Session::set('error', 'Yêu cầu không hợp lệ!');
            header('Location: /checkout');
            exit;
        }

        $userId = Session::get('user')['id'];
        $cartItems = $this->cartModel->getByUser($userId);
        if (empty($cartItems)) {
            Session::set('error', 'Giỏ hàng trống!');
            header('Location: /cart');
            exit;
        }

        $details = [
            'fullname' => $_POST['fullname'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'pincode' => $_POST['pincode'] ?? '',
            'state' => $_POST['state'] ?? '',
            'town_city' => $_POST['town_city'] ?? '',
            'house_no' => $_POST['house_no'] ?? '',
            'road_name' => $_POST['road_name'] ?? '',
            'landmark' => $_POST['landmark'] ?? ''
        ];
        $paymentMethod = $_POST['payment_method'] ?? '';

        // Validate
        foreach ($details as $key => $value) {
            if (empty($value)) {
                Session::set('error', 'Vui lòng điền đầy đủ thông tin!');
                header('Location: /checkout');
                exit;
            }
        }
        if (!in_array($paymentMethod, ['cod', 'payos'])) {
            Session::set('error', 'Phương thức thanh toán không hợp lệ!');
            header('Location: /checkout');
            exit;
        }

        // Tạo đơn hàng cho từng sản phẩm trong giỏ
        $orderIds = [];
        foreach ($cartItems as $item) {
            // Log product_id để debug
            error_log("Processing product_id: " . $item['product_id']);

            $subtotal = $item['price'] * $item['quantity'];
            $vat = $subtotal * 0.1;
            $totalPrice = $subtotal + $vat;

            // Lấy seller_id từ products
            $stmt = $this->db->prepare("SELECT seller_id FROM products WHERE id = ?");
            $stmt->execute([$item['product_id']]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);

            // Kiểm tra lỗi truy vấn
            if ($stmt->errorCode() !== '00000') {
                $errorInfo = $stmt->errorInfo();
                error_log("Database query error for product_id {$item['product_id']}: " . $errorInfo[2]);
                Session::set('error', 'Lỗi truy vấn cơ sở dữ liệu: ' . $errorInfo[2]);
                header('Location: /checkout');
                exit;
            }

            // Kiểm tra xem sản phẩm có tồn tại và có seller_id không
            if (!$result || !isset($result['seller_id']) || empty($result['seller_id'])) {
                error_log("Product {$item['product_id']} not found or has no seller_id");
                // Xóa sản phẩm không hợp lệ khỏi giỏ hàng
                $this->cartModel->remove($userId, $item['product_id']);
                Session::set('error', 'Sản phẩm ' . $item['product_id'] . ' không tồn tại hoặc không có người bán! Đã xóa khỏi giỏ hàng.');
                header('Location: /cart');
                exit;
            }

            $sellerId = $result['seller_id'];

            // Tạo đơn hàng
            $orderId = $this->orderModel->create($userId, $sellerId, $item['product_id'], $item['quantity'], $totalPrice);
            $this->orderModel->addDetail($orderId, $details);
            $this->orderModel->createPayment($orderId, $paymentMethod, $totalPrice);
            $orderIds[] = $orderId;

            // Xử lý thanh toán
            if ($paymentMethod === 'cod') {
                $this->orderModel->updateStatus($orderId, 'pending');
                // Lưu giao dịch cho COD
                $this->createTransaction($orderId, $totalPrice, 'cod', 'pending', null);
            } else {
                // PayOS
                $payosResponse = $this->generatePayosUrl($orderId, $totalPrice, $details);
                // Log phản hồi PayOS để debug
                error_log("PayOS response for order $orderId: " . print_r($payosResponse, true));

                if (!isset($payosResponse['error']) || $payosResponse['error'] !== 0 || !isset($payosResponse['data']['checkoutUrl'])) {
                    $errorMessage = $payosResponse['message'] ?? 'Không thể tạo link thanh toán PayOS';
                    error_log("PayOS error for order $orderId: $errorMessage");
                    Session::set('error', 'Lỗi khi tạo link thanh toán PayOS: ' . $errorMessage);
                    header('Location: /checkout');
                    exit;
                }

                $this->cartModel->clear($userId); // Xóa giỏ hàng trước khi redirect
                header('Location: ' . $payosResponse['data']['checkoutUrl']);
                exit;
            }
        }

        // Xóa giỏ hàng sau khi tạo tất cả đơn hàng
        $this->cartModel->clear($userId);
        Session::set('success', 'Đặt hàng thành công! Đơn hàng sẽ được giao sớm.');
        header('Location: /order/confirmation/' . end($orderIds)); // Redirect đến đơn hàng cuối
        exit;
    }

    private function generatePayosUrl($orderId, $amount, $details)
    {
        $clientId = "36298a76-5648-4201-8bb0-88a4482c3c8d";
        $apiKey = "1a37d1a3-2185-429c-8fc3-fc98d3e7e1ab";
        $checksumKey = "0c35a0a847e96258d7095565ba4367988ed2ab0293c3c197760374702bbe7854";
        $returnUrl = "https://455a-125-212-151-205.ngrok-free.app/checkout/callback";
        $cancelUrl = "https://455a-125-212-151-205.ngrok-free.app/checkout";

        // Chuẩn hóa amount
        $amount = (int)$amount;
        if ($amount <= 0) {
            error_log("Số tiền không hợp lệ cho đơn hàng $orderId: $amount");
            return ['error' => 1, 'message' => 'Số tiền không hợp lệ'];
        }

        // Chuẩn bị dữ liệu cho PayOS
        $data = [
            'orderCode' => (int)$orderId,
            'amount' => $amount,
            'description' => "Thanh toán đơn hàng $orderId",
            'returnUrl' => $returnUrl,
            'cancelUrl' => $cancelUrl,
            'buyerName' => trim($details['fullname'] ?? ''),
            'buyerPhone' => trim($details['phone'] ?? ''),
            'buyerAddress' => trim($details['state'] ?? ''),
            'currency' => 'VND',
            'expiredAt' => time() + (24 * 60 * 60), // Hết hạn sau 24 giờ
            'items' => json_encode([
                [
                    'name' => "Đơn hàng $orderId",
                    'quantity' => 1,
                    'price' => $amount
                ]
            ])
        ];

        // Kiểm tra các trường bắt buộc
        if (empty($data['buyerName']) || empty($data['buyerPhone']) || empty($data['buyerAddress'])) {
            error_log("Thiếu thông tin người mua, số điện thoại hoặc địa chỉ cho đơn hàng $orderId: " . print_r($data, true));
            return ['error' => 1, 'message' => 'Thiếu thông tin người mua, số điện thoại hoặc địa chỉ'];
        }

        // Chuẩn hóa buyerName để tránh lỗi ký tự tiếng Việt
        $data['buyerName'] = mb_convert_encoding($data['buyerName'], 'UTF-8', 'auto');

        // Chuẩn bị dữ liệu cho chữ ký (loại bỏ items khỏi chữ ký)
        $dataForSignature = [
            'orderCode' => $data['orderCode'],
            'amount' => $data['amount'],
            'description' => $data['description'],
            'returnUrl' => $data['returnUrl'],
            'cancelUrl' => $data['cancelUrl'],
            'buyerName' => $data['buyerName'],
            'buyerPhone' => $data['buyerPhone'],
            'buyerAddress' => $data['buyerAddress'],
            'currency' => $data['currency'],
            'expiredAt' => $data['expiredAt']
        ];

        // Sắp xếp dữ liệu theo key
        ksort($dataForSignature);

        // Tạo chuỗi dữ liệu cho chữ ký
        $dataStr = http_build_query($dataForSignature);
        $signature = hash_hmac('sha256', $dataStr, $checksumKey);

        // Ghi log để kiểm tra
        error_log("PayOS request data for order $orderId: " . print_r($data, true));
        error_log("Data string for signature: $dataStr");
        error_log("Generated signature: $signature");

        // Gửi yêu cầu tới PayOS
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api-merchant.payos.vn/v2/payment-requests");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "x-client-id: $clientId",
            "x-api-key: $apiKey",
            "x-signature: $signature"
        ]);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_FAILONERROR, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            $error = curl_error($ch);
            error_log("Lỗi cURL cho đơn hàng $orderId: $error");
            curl_close($ch);
            return ['error' => 1, 'message' => 'Lỗi kết nối PayOS: ' . $error];
        }

        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 400) {
            error_log("Lỗi HTTP PayOS cho đơn hàng $orderId: HTTP $httpCode, Response: $response");
            return ['error' => 1, 'message' => "Lỗi PayOS HTTP $httpCode"];
        }

        $decodedResponse = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            error_log("Lỗi giải mã JSON cho đơn hàng $orderId: " . json_last_error_msg());
            return ['error' => 1, 'message' => 'Lỗi giải mã phản hồi PayOS'];
        }

        return $decodedResponse;
    }

    private function createTransaction($orderId, $amount, $paymentMethod, $status, $transactionId = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO transactions (order_id, amount, payment_method, status, transaction_id, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$orderId, $amount, $paymentMethod, $status, $transactionId]);
    }

    public function payosCallback()
    {
        $checksumKey = "0c35a0a847e96258d7095565ba4367988ed2ab0293c3c197760374702bbe7854";
        $inputData = $_GET;
        $signature = $inputData['signature'] ?? '';
        $orderCode = $inputData['orderCode'] ?? '';
        $amount = $inputData['amount'] ?? 0;
        $status = $inputData['status'] ?? '';

        // Xác minh chữ ký
        unset($inputData['signature']);
        ksort($inputData);
        $dataStr = http_build_query($inputData);
        $expectedSignature = hash_hmac('sha256', $dataStr, $checksumKey);

        if ($signature !== $expectedSignature) {
            Session::set('error', 'Chữ ký không hợp lệ!');
            header('Location: /checkout');
            exit;
        }

        $orderId = $orderCode; // orderCode của PayOS chính là orderId
        if ($status === 'PAID') {
            $this->orderModel->updatePaymentStatus($orderId, 'completed', $orderCode);
            $this->orderModel->updateStatus($orderId, 'processing');
            // Lưu giao dịch cho PayOS thành công
            $this->createTransaction($orderId, $amount, 'payos', 'completed', $orderCode);
            Session::set('success', 'Thanh toán thành công!');
            header('Location: /order/confirmation/' . $orderId);
            exit;
        } else {
            $this->orderModel->updatePaymentStatus($orderId, 'failed', $orderCode);
            $this->orderModel->updateStatus($orderId, 'cancelled');
            // Lưu giao dịch cho PayOS thất bại
            $this->createTransaction($orderId, $amount, 'payos', 'failed', $orderCode);
            Session::set('error', 'Thanh toán thất bại!');
            header('Location: /checkout');
            exit;
        }
    }

    public function confirmation($orderId)
    {
        if (!Session::get('user')) {
            header('Location: /login');
            exit;
        }
        require_once __DIR__ . '/../Views/checkout/confirmation.php';
    }

    public function payOrder($orderId)
    {
        // Kiểm tra người dùng đã đăng nhập
        if (!Session::get('user')) {
            Session::set('error', 'Vui lòng đăng nhập để thực hiện thanh toán!');
            header('Location: /login');
            exit;
        }

        // Lấy thông tin đơn hàng
        $order = $this->orderModel->getOrderById($orderId);
        if (!$order) {
            Session::set('error', 'Đơn hàng không tồn tại!');
            header('Location: /profile/my-orders');
            exit;
        }

        // Kiểm tra quyền sở hữu đơn hàng
        if ($order['buyer_id'] !== Session::get('user')['id']) {
            Session::set('error', 'Bạn không có quyền thanh toán đơn hàng này!');
            header('Location: /profile/my-orders');
            exit;
        }

        // Lấy trạng thái thanh toán
        $stmt = $this->db->prepare("SELECT status, payment_method FROM payment WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $payment = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$payment || ($payment['status'] !== 'failed' && $order['status'] !== 'cancelled')) {
            Session::set('error', 'Đơn hàng không thể thanh toán lại!');
            header('Location: /profile/my-orders');
            exit;
        }

        // Lấy thông tin chi tiết đơn hàng
        $stmt = $this->db->prepare("SELECT * FROM order_detail WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $detail = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$detail) {
            Session::set('error', 'Không tìm thấy thông tin chi tiết đơn hàng!');
            header('Location: /profile/my-orders');
            exit;
        }

        // Gọi lại hàm tạo link thanh toán PayOS
        $details = [
            'fullname' => $detail['fullname'] ?? '',
            'phone' => $detail['phone'] ?? '',
            'state' => $detail['state'] ?? ''
        ];

        $payosResponse = $this->generatePayosUrl($orderId, $order['total_amount'], $details);

        if (!isset($payosResponse['error']) || $payosResponse['error'] !== 0 || !isset($payosResponse['data']['checkoutUrl'])) {
            $errorMessage = $payosResponse['message'] ?? 'Không thể tạo link thanh toán PayOS';
            error_log("PayOS error khi thanh toán lại đơn hàng $orderId: $errorMessage");
            Session::set('error', 'Lỗi khi tạo link thanh toán PayOS: ' . $errorMessage);
            header('Location: /profile/my-orders');
            exit;
        }

        // Redirect sang trang thanh toán PayOS
        header('Location: ' . $payosResponse['data']['checkoutUrl']);
        exit;
    }
}
