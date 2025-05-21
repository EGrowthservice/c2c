<?php

namespace App\Helpers;

use App\Helpers\Session;

// Bắt đầu session
Session::start();

// Kiểm tra đăng nhập
if (!Session::get('user')) {
    Session::set('error', 'Vui lòng đăng nhập để chỉnh sửa sản phẩm!');
    header('Location: /login');
    exit;
}

// Xác định đường dẫn
$userLink = Session::get('user') ? '/profile' : '/login';
$currentUserId = Session::get('user')['id'] ?? null;

// Xử lý thông báo
$error = Session::get('error');
$success = Session::get('success');
if ($error) {
    Session::unset('error');
}
if ($success) {
    Session::unset('success');
}

// Bao gồm header
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/./linkcss.php';

?>

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="my-account container">


        <div class="row">
            <!-- Sidebar Menu -->
            <div class="col-lg-3">
                <ul class="account-nav">
                    <li><a href="/profile" class="account-nav__link">Tổng quan</a></li>
                    <li><a href="/profile/orders" class="account-nav__link">Đơn hàng</a></li>
                    <li><a href="/profile/products" class="account-nav__link active">Sản phẩm</a></li>
                    <li><a href="/profile/my-orders" class="account-nav__link">Đơn hàng của tôi</a></li>
                    <li><a href="/profile/account-details" class="account-nav__link">Chi tiết tài khoản</a></li>
                    <li><a href="/logout" class="account-nav__link">Đăng xuất</a></li>
                </ul>
            </div>

            <!-- Nội dung chính -->
            <div class="col-lg-9">
                <form method="POST" action="/products/edit/<?= htmlspecialchars($product['id']) ?>" enctype="multipart/form-data" id="editProductForm">
                    <input type="hidden" name="_token" value="<?= htmlspecialchars(Session::get('csrf_token')) ?>">
                    <div class="mb-3">
                        <label for="title" class="form-label">Tiêu đề <span class="text-danger">*</span></label>
                        <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($product['title']) ?>" placeholder="Nhập tiêu đề sản phẩm" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Danh mục <span class="text-danger">*</span></label>
                        <select name="category_id" id="category_id" class="form-control" required>
                            <option value="">Chọn danh mục</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= htmlspecialchars($category['id']) ?>" <?= $category['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($category['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Mô tả <span class="text-danger">*</span></label>
                        <textarea name="description" id="description" class="form-control" rows="5" placeholder="Mô tả chi tiết sản phẩm" required><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Giá (VND) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="price" class="form-control" step="1000" min="0" value="<?= htmlspecialchars($product['price']) ?>" placeholder="Nhập giá sản phẩm" required>
                    </div>
                    <div class="mb-3">
                        <label for="image" class="form-label">Hình ảnh</label>
                        <?php if (!empty($product['image'])): ?>
                            <div class="mb-2">
                                <img src="/Uploads/<?= htmlspecialchars($product['image']) ?>" alt="Current Image" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image" id="image" class="form-control" accept="image/*">
                        <div id="imagePreview" class="mt-2" style="display: none;">
                            <img src="" alt="Image Preview" style="max-width: 200px; max-height: 200px; object-fit: cover;">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary" title="Cập nhật sản phẩm">
                        <i class="bi bi-save"></i> Cập nhật sản phẩm
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<!-- Tích hợp SweetAlert2 và Bootstrap Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="assets/js/plugins/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/plugins/bootstrap-slider.min.js"></script>
<script src="assets/js/plugins/swiper.min.js"></script>
<script>
    // Tránh tải trùng lặp countdown.js và theme.js
    if (!window.countdownLoaded) {
        document.write('<script src="assets/js/plugins/countdown.js"><\/script>');
        window.countdownLoaded = true;
    }
    if (!window.themeLoaded) {
        document.write('<script src="assets/js/theme.js"><\/script>');
        window.themeLoaded = true;
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Hiển thị thông báo từ Session
        <?php if ($error): ?>
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: '<?= htmlspecialchars($error) ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#d33'
            });
        <?php endif; ?>
        <?php if ($success): ?>
            Swal.fire({
                icon: 'success',
                title: 'Thành công',
                text: '<?= htmlspecialchars($success) ?>',
                confirmButtonText: 'OK',
                confirmButtonColor: '#3085d6',
                timer: 2000,
                timerProgressBar: true
            });
        <?php endif; ?>

        // Preview hình ảnh
        const imageInput = document.getElementById('image');
        const imagePreview = document.getElementById('imagePreview');
        const previewImg = imagePreview.querySelector('img');

        if (imageInput) {
            imageInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file && file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        imagePreview.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                } else {
                    imagePreview.style.display = 'none';
                    previewImg.src = '';
                }
            });
        }

        // Validate form trước khi submit
        const form = document.getElementById('editProductForm');
        if (form) {
            form.addEventListener('submit', function(e) {
                const title = document.getElementById('title').value.trim();
                const category = document.getElementById('category_id').value;
                const description = document.getElementById('description').value.trim();
                const price = document.getElementById('price').value;

                if (!title) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng nhập tiêu đề sản phẩm!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
                if (!category) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng chọn danh mục!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
                if (!description) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng nhập mô tả sản phẩm!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
                if (!price || price <= 0) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Vui lòng nhập giá hợp lệ (lớn hơn 0)!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                    return;
                }
            });
        }
    });
</script>

<style>
    .account-nav {
        list-style: none;
        padding: 0;
    }

    .account-nav__link {
        display: block;
        padding: 10px 0;
        color: #333;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }

    .account-nav__link:hover,
    .account-nav__link.active {
        color: #007bff;
    }

    .form-control,
    .form-select {
        border-radius: 0.375rem;
        transition: border-color 0.2s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }

    .form-label {
        font-weight: 500;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (max-width: 576px) {

        .form-control,
        .form-select {
            font-size: 0.9rem;
        }

        .btn-primary {
            font-size: 0.9rem;
        }
    }
</style>

<?php
include __DIR__ . '/../layouts/footer.php';
?>