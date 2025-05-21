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
        if (!in_array($paymentMethod, ['cod', 'vnpay'])) {
            Session::set('error', 'Phương thức thanh toán không hợp lệ!');
            header('Location: /checkout');
            exit;
        }

        // Tạo đơn hàng cho từng sản phẩm trong giỏ
        $orderIds = [];
        foreach ($cartItems as $item) {
            $subtotal = $item['price'] * $item['quantity'];
            $vat = $subtotal * 0.1;
            $totalPrice = $subtotal + $vat;

            // Lấy seller_id từ products
            $stmt = $this->db->prepare("SELECT seller_id FROM products WHERE id = ?");
            $stmt->execute([$item['product_id']]);
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            $sellerId = $result['seller_id'] ?? null;

            if (!$sellerId) {
                Session::set('error', 'Thông tin người bán không hợp lệ cho sản phẩm #' . $item['product_id']);
                header('Location: /checkout');
                exit;
            }

            // Tạo đơn hàng
            $orderId = $this->orderModel->create($userId, $sellerId, $item['product_id'], $item['quantity'], $totalPrice);
            $this->orderModel->addDetail($orderId, $details);
            $this->orderModel->createPayment($orderId, $paymentMethod, $totalPrice);
            $orderIds[] = $orderId;

            // Xử lý thanh toán
            if ($paymentMethod === 'cod') {
                $this->orderModel->updateStatus($orderId, 'pending');
                // Lưu giao dịch cho COD
                $this->createTransaction($orderId, $totalPrice, 'cod', 'pending');
            } else {
                // VNPay
                $vnpayUrl = $this->generateVnpayUrl($orderId, $totalPrice, $details);
                $this->cartModel->clear($userId); // Xóa giỏ hàng trước khi redirect
                header('Location: ' . $vnpayUrl);
                exit;
            }
        }

        // Xóa giỏ hàng sau khi tạo tất cả đơn hàng
        $this->cartModel->clear($userId);
        Session::set('success', 'Đặt hàng thành công! Đơn hàng sẽ được giao sớm.');
        header('Location: /order/confirmation/' . end($orderIds)); // Redirect đến đơn hàng cuối
        exit;
    }

    private function generateVnpayUrl($orderId, $amount, $details)
    {
        $vnp_TmnCode = "1N55MPDP";
        $vnp_HashSecret = "YOE5I4XR9E5T09VVCUNR9EA9L8HERJH4";
        $vnp_Url = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnp_Returnurl = "http://localhost:8080/checkout/vnpay-callback";

        $vnp_TxnRef = $orderId;
        $vnp_OrderInfo = "Thanh toan don hang #$orderId";
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $amount * 100;
        $vnp_Locale = "vn";
        $vnp_BankCode = "";
        $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
        date_default_timezone_set('Asia/Ho_Chi_Minh');
        $vnp_CreateDate = date('YmdHis');
        $vnp_ExpireDate = date('YmdHis', strtotime('+15 minutes'));

        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => $vnp_CreateDate,
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
            "vnp_ExpireDate" => $vnp_ExpireDate
        ];

        ksort($inputData);
        $query = http_build_query($inputData);
        $hashdata = $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        return $vnp_Url . "?" . $query . "&vnp_SecureHash=" . $vnpSecureHash;
    }

    private function createTransaction($orderId, $amount, $paymentMethod, $status)
    {
        $stmt = $this->db->prepare("
            INSERT INTO transactions (order_id, amount, payment_method, status, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$orderId, $amount, $paymentMethod, $status]);
    }

    public function vnpayCallback()
    {
        $vnp_HashSecret = "YOE5I4XR9E5T09VVCUNR9EA9L8HERJH4";
        $inputData = $_GET;
        $vnp_SecureHash = $inputData['vnp_SecureHash'];
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashdata = http_build_query($inputData);
        $hash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);

        $orderId = $inputData['vnp_TxnRef'];
        $amount = $inputData['vnp_Amount'] / 100;
        $transactionId = $inputData['vnp_TransactionNo'];

        if ($hash === $vnp_SecureHash && $inputData['vnp_ResponseCode'] == '00') {
            $this->orderModel->updatePaymentStatus($orderId, 'completed', $transactionId);
            $this->orderModel->updateStatus($orderId, 'processing');
            // Lưu giao dịch cho VNPay thành công
            $this->createTransaction($orderId, $amount, 'vnpay', 'completed');
            Session::set('success', 'Thanh toán thành công!');
            header('Location: /order/confirmation/' . $orderId);
            exit;
        } else {
            $this->orderModel->updatePaymentStatus($orderId, 'failed', $transactionId);
            $this->orderModel->updateStatus($orderId, 'cancelled');
            // Lưu giao dịch cho VNPay thất bại
            $this->createTransaction($orderId, $amount, 'vnpay', 'failed');
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

        // Kiểm tra xem đơn hàng có thuộc về người dùng hiện tại không
        if ($order['buyer_id'] !== Session::get('user')['id']) {
            Session::set('error', 'Bạn không có quyền thanh toán đơn hàng này!');
            header('Location: /profile/my-orders');
            exit;
        }

        // Kiểm tra trạng thái đơn hàng và thanh toán
        $stmt = $this->db->prepare("SELECT status, payment_method FROM payment WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $payment = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$payment || ($payment['status'] !== 'failed' && $order['status'] !== 'cancelled')) {
            Session::set('error', 'Đơn hàng không thể thanh toán lại!');
            header('Location: /profile/my-orders');
            exit;
        }

        // Lấy chi tiết đơn hàng để sử dụng cho VNPay
        $stmt = $this->db->prepare("SELECT * FROM order_detail WHERE order_id = ?");
        $stmt->execute([$orderId]);
        $orderDetails = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$orderDetails) {
            Session::set('error', 'Không tìm thấy thông tin chi tiết đơn hàng!');
            header('Location: /profile/my-orders');
            exit;
        }

        $details = [
            'fullname' => $orderDetails['fullname'],
            'phone' => $orderDetails['phone'],
            'pincode' => $orderDetails['pincode'],
            'state' => $orderDetails['state'],
            'town_city' => $orderDetails['town_city'],
            'house_no' => $orderDetails['house_no'],
            'road_name' => $orderDetails['road_name'],
            'landmark' => $orderDetails['landmark']
        ];

        $paymentMethod = $payment['payment_method'] ?? 'vnpay'; // Sử dụng phương thức thanh toán đã lưu, mặc định là VNPay

        // Xử lý thanh toán
        if ($paymentMethod === 'cod') {
            // Cập nhật trạng thái đơn hàng và thanh toán cho COD
            $this->orderModel->updatePaymentStatus($orderId, 'pending', null);
            $this->orderModel->updateStatus($orderId, 'pending');
            // Lưu giao dịch cho COD
            $this->createTransaction($orderId, $order['total_price'], 'cod', 'pending');
            Session::set('success', 'Đã yêu cầu thanh toán lại bằng COD!');
            header('Location: /order/confirmation/' . $orderId);
            exit;
        } else {
            // VNPay
            $vnpayUrl = $this->generateVnpayUrl($orderId, $order['total_price'], $details);
            header('Location: ' . $vnpayUrl);
            exit;
        }
    }
}
