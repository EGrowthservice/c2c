 <?php

    use App\Helpers\Session;

    // Kiểm tra đăng nhập và vai trò
    $user = Session::get('user');
    if (!$user) {
        header('Location: /login');
        exit;
    }
    ?>
 <div class="col-lg-3 mb-3">
     <ul class="account-nav">
         <li><a href="/profile" class="account-nav__link">Tổng quan</a></li>
         <?php if ($user['role'] === 'admin'): ?>

             <li><a href="/admin" class="account-nav__link">QUẢN LÝ - ADMIN</a></li>
         <?php endif; ?>

         <li><a href="/profile/orders" class="account-nav__link">Đơn hàng</a></li>
         <li><a href="/profile/products" class="account-nav__link">Sản phẩm</a></li>
         <li><a href="/profile/my-orders" class="account-nav__link">Đơn hàng của tôi</a></li>
         <li><a href="/profile/account-details" class="account-nav__link">Chi tiết tài khoản</a></li>
         <li><a href="/logout" class="account-nav__link">Đăng xuất</a></li>
     </ul>
 </div>