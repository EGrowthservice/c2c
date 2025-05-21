<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Order;
use App\Helpers\Session;

class ProfileController
{
    private $productModel;
    private $orderModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->orderModel = new Order();
    }

    // Trang Tổng quan
    public function index()
    {
        if (!Session::get('user')) {
            Session::set('error', 'Vui lòng đăng nhập để xem hồ sơ!');
            header('Location: /login');
            exit;
        }
        require_once __DIR__ . '/../Views/profile/index.php';
    }

    // Trang Quản lý đơn hàng
    public function orders()
    {
        if (!Session::get('user')) {
            Session::set('error', 'Vui lòng đăng nhập để xem đơn hàng!');
            header('Location: /login');
            exit;
        }
        $userId = Session::get('user')['id'];
        $orders = $this->orderModel->getOrdersBySellerId($userId);
        require_once __DIR__ . '/../Views/profile/orders.php';
    }
    // Trang Đơn hàng của tôi (đã mua)
    public function myOrders()
    {
        if (!Session::get('user')) {
            Session::set('error', 'Vui lòng đăng nhập để xem đơn hàng!');
            header('Location: /login');
            exit;
        }
        $userId = Session::get('user')['id'];
        $orders = $this->orderModel->getOrdersByBuyerId($userId);
        require_once __DIR__ . '/../Views/profile/my-orders.php';
    }

    // Trang Quản lý sản phẩm
    public function products()
    {
        if (!Session::get('user')) {
            Session::set('error', 'Vui lòng đăng nhập để xem sản phẩm!');
            header('Location: /login');
            exit;
        }
        $userId = Session::get('user')['id'];
        $products = $this->productModel->getProductsByUserId($userId);
        require_once __DIR__ . '/../Views/profile/products.php';
    }

    // Trang Chi tiết tài khoản
    public function accountDetails()
    {
        if (!Session::get('user')) {
            Session::set('error', 'Vui lòng đăng nhập để xem chi tiết tài khoản!');
            header('Location: /login');
            exit;
        }
        require_once __DIR__ . '/../Views/profile/account-details.php';
    }
}
