<?php

use App\Controllers\AuthController;
use App\Controllers\ProductController;
use App\Controllers\OrderController;
use App\Controllers\FavoriteController;
use App\Controllers\ReviewController;
use App\Controllers\AdminController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\ContactController;
use App\Controllers\PaymentController;
use App\Controllers\ProfileController;
use App\Controllers\ReportController;
use App\Controllers\SellerController;
use App\Controllers\HomeController;

// === Trang chính ===
$router->get('/', [HomeController::class, 'index']);

// === Auth ===
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->post('/forgot-password', [AuthController::class, 'forgotPassword']);
$router->get('/reset-password', [AuthController::class, 'resetPassword']);
$router->post('/reset-password', [AuthController::class, 'resetPassword']);
$router->get('/profile', [AuthController::class, 'profile']);
$router->get('/profile/change-password', [AuthController::class, 'changePassword']);
$router->post('/profile/change-password', [AuthController::class, 'changePassword']);

// === Hồ sơ người dùng ===
$router->get('/profile/orders', [ProfileController::class, 'orders']);
$router->get('/profile/products', [ProfileController::class, 'products']);
$router->get('/profile/account-details', [ProfileController::class, 'accountDetails']);
$router->get('/profile/my-orders', [ProfileController::class, 'myOrders']);

// === Sản phẩm ===
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/search', [ProductController::class, 'index']);
$router->get('/products/create', [ProductController::class, 'create']);
$router->post('/products/create', [ProductController::class, 'create']);
$router->get('/products/edit/{id}', [ProductController::class, 'edit']);
$router->post('/products/edit/{id}', [ProductController::class, 'edit']);
$router->get('/products/delete/{id}', [ProductController::class, 'delete']);
$router->get('/products/{id}', [ProductController::class, 'show']);

// === Yêu thích ===
$router->get('/favorites', [FavoriteController::class, 'index']);
$router->post('/favorites/add', [FavoriteController::class, 'add']);
$router->post('/favorites/remove/{id}', [FavoriteController::class, 'remove']);
// === Đánh giá ===
$router->get('/reviews/create/{id}', [ReviewController::class, 'create']);
$router->post('/reviews/create/{id}', [ReviewController::class, 'create']);

// === Tố cáo (Report) ===
$router->get('/reports/create/{id}', [ReportController::class, 'create']);
$router->post('/reports/create/{id}', [ReportController::class, 'create']);

// === Đơn hàng ===
$router->post('/orders/cancel/{id}', [OrderController::class, 'cancel']);
$router->get('/orders/track/{id}', [OrderController::class, 'track']);

// === Quản lý đơn hàng nhà bán ===
$router->get('/seller/orders', [OrderController::class, 'sellerOrders']);
$router->get('/seller/orders/update/{id}', [OrderController::class, 'updateOrder']);
$router->post('/seller/orders/update/{id}', [OrderController::class, 'updateOrder']);

// === Người bán ===
$router->get('/sellers/{id}', [SellerController::class, 'show']);
$router->get('/sellers/rate/{id}', [SellerController::class, 'rate']);
$router->post('/sellers/rate/{id}', [SellerController::class, 'rate']);

// === Thanh toán ===
$router->get('/checkout', [CheckoutController::class, 'index']);
$router->post('/checkout/process', [CheckoutController::class, 'process']);
$router->get('/checkout/vnpay-callback', [CheckoutController::class, 'vnpayCallback']);
$router->get('/order/confirmation/{id}', [CheckoutController::class, 'confirmation']);
$router->get('/orders/pay/{id}', [CheckoutController::class, 'payOrder']);

// Thêm vào giỏ hàng
$router->post('/cart/add', [CartController::class, 'add']);
$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/remove/{id}', [CartController::class, 'remove']);

// Liên hệ
$router->get('/contact', [ContactController::class, 'index']);
$router->post('/contact', [ContactController::class, 'index']);

// === Quản trị (Admin) ===
$router->get('/admin', [AdminController::class, 'dashboard']);

// -- Quản lý sản phẩm --
$router->get('/admin/products', [AdminController::class, 'products']);
$router->get('/admin/search-products', [AdminController::class, 'searchProducts']);
$router->get('/admin/products/status/{id}/{status}', [AdminController::class, 'updateProductStatus']);
$router->get('/admin/products/view/{id}', [AdminController::class, 'view_product']);


// -- Quản lý danh mục sản phẩm --
$router->get('/admin/categories', [AdminController::class, 'index']);
$router->get('/admin/categories/create', [AdminController::class, 'createCategory']);
$router->post('/admin/categories/create', [AdminController::class, 'createCategory']);
$router->get('/admin/categories/edit/{id}', [AdminController::class, 'editCategory']);
$router->post('/admin/categories/edit/{id}', [AdminController::class, 'editCategory']);
$router->post('/admin/categories/delete/{id}', [AdminController::class, 'deleteCategory']);

// -- Quản lý người dùng --
$router->get('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/search-users', [AdminController::class, 'searchUsers']);
$router->get('/admin/users/activate/(\d+)', function ($id) {
    (new \App\Controllers\AdminController)->toggleUserStatus($id, 'activate');
});
$router->get('/admin/users/deactivate/(\d+)', function ($id) {
    (new \App\Controllers\AdminController)->toggleUserStatus($id, 'deactivate');
});

// -- Quản lý tố cáo --
$router->post('/admin/reports/delete/{id}', [AdminController::class, 'deleteReport']);
$router->get('/admin/reports', [AdminController::class, 'reports']);
$router->get('/admin/users/view/{id}', [AdminController::class, 'view_user']);

// Liên hệ
$router->get('/admin/contacts', [AdminController::class, 'contacts']);
