<?php

namespace App\Helpers;

use App\Helpers\Session;

// Bắt đầu session
Session::start();

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
include __DIR__ . '/../products/linkcss.php';

?>

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">


        <ul class="nav nav-tabs mb-5" id="forgot_password_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore active" id="forgot-password-tab" data-bs-toggle="tab" href="#tab-item-forgot-password"
                    role="tab" aria-controls="tab-item-forgot-password" aria-selected="true">Quên mật khẩu</a>
            </li>
        </ul>
        <div class="tab-content pt-2" id="forgot_password_tab_content">
            <div class="tab-pane fade show active" id="tab-item-forgot-password" role="tabpanel" aria-labelledby="forgot-password-tab">
                <div class="login-form">
                    <form id="forgot-password-form" method="POST" action="/forgot-password" name="forgot-password-form" class="needs-validation" novalidate>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control form-control_gray" name="email" id="email" required>
                            <label for="email">Email *</label>
                        </div>
                        <button class="btn btn-primary w-100 text-uppercase" type="submit">Gửi link đặt lại</button>
                        <div class="customer-option mt-4 text-center">
                            <a href="/login" class="btn-text">Quay lại đăng nhập</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
</main>

<!-- Tích hợp SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('forgot-password-form');
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!form.checkValidity()) {
                form.classList.add('was-validated');
                return;
            }

            const formData = new FormData(form);
            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    Swal.fire({
                        icon: data.success ? 'success' : 'error',
                        title: data.success ? 'Thành công' : 'Lỗi',
                        text: data.message,
                        confirmButtonText: 'OK',
                        confirmButtonColor: data.success ? '#3085d6' : '#d33',
                        timer: data.success ? 2000 : null,
                        timerProgressBar: true
                    });
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã xảy ra lỗi khi gửi yêu cầu!',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#d33'
                    });
                });
        });

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
    });
</script>

<?php
// Bao gồm footer
include __DIR__ . '/../layouts/footer.php';
?>