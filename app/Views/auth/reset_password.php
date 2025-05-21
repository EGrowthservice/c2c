<?php

namespace App\Helpers;

use App\Helpers\Session;

// Start session
Session::start();

// Handle notifications
$error = Session::get('error');
$success = Session::get('success');
if ($error) {
    Session::unset('error');
}
if ($success) {
    Session::unset('success');
}

// Include header and CSS
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../products/linkcss.php';
?>

<main class="pt-90">
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
        <ul class="nav nav-tabs mb-5" id="reset_password_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore active" id="reset-password-tab" data-bs-toggle="tab" href="#tab-item-reset-password"
                    role="tab" aria-controls="tab-item-reset-password" aria-selected="true">Đặt lại mật khẩu</a>
            </li>
        </ul>
        <div class="tab-content pt-2" id="reset_password_tab_content">
            <div class="tab-pane fade show active" id="tab-item-reset-password" role="tabpanel" aria-labelledby="reset-password-tab">
                <div class="login-form">
                    <form id="reset-password-form" method="POST" action="/reset-password" name="reset-password-form" class="needs-validation" novalidate>
                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control form-control_gray" name="new_password" id="new_password" required>
                            <label for="new_password">Mật khẩu mới *</label>
                            <div class="invalid-feedback">Vui lòng nhập mật khẩu mới!</div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="password" class="form-control form-control_gray" name="confirm_password" id="confirm_password" required>
                            <label for="confirm_password">Xác nhận mật khẩu *</label>
                            <div class="invalid-feedback">Vui lòng xác nhận mật khẩu!</div>
                        </div>
                        <button class="btn btn-primary w-100 text-uppercase" type="submit">Đặt lại mật khẩu</button>
                        <div class="customer-option mt-4 text-center">
                            <a href="/login" class="btn-text">Quay lại đăng nhập</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</main>

<!-- Include SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('reset-password-form');
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
                    }).then(() => {
                        if (data.success && data.redirect) {
                            window.location.href = data.redirect;
                        }
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

        // Display session notifications
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
// Include footer
include __DIR__ . '/../layouts/footer.php';
?>