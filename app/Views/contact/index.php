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
    <div class="mb-4 pb-4"></div>
    <section class="login-register container">
        <ul class="nav nav-tabs mb-5" id="contact_tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link nav-link_underscore active" id="contact-tab" data-bs-toggle="tab" href="#tab-item-contact"
                    role="tab" aria-controls="tab-item-contact" aria-selected="true">Liên hệ</a>
            </li>
        </ul>
        <div class="tab-content pt-2" id="contact_tab_content">
            <div class="tab-pane fade show active" id="tab-item-contact" role="tabpanel" aria-labelledby="contact-tab">
                <div class="login-form">
                    <form id="contact-form" method="POST" action="/contact" name="contact-form" class="needs-validation" novalidate>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-control_gray" name="name" id="name" required>
                            <label for="name">Họ và tên *</label>
                            <div class="invalid-feedback">Vui lòng nhập họ và tên!</div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="email" class="form-control form-control_gray" name="email" id="email" required>
                            <label for="email">Email *</label>
                            <div class="invalid-feedback">Vui lòng nhập email hợp lệ!</div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control form-control_gray" name="subject" id="subject" required>
                            <label for="subject">Chủ đề *</label>
                            <div class="invalid-feedback">Vui lòng nhập chủ đề!</div>
                        </div>
                        <div class="form-floating mb-3">
                            <textarea class="form-control form-control_gray" name="message" id="message" required style="height: 100px;"></textarea>
                            <label for="message">Nội dung *</label>
                            <div class="invalid-feedback">Vui lòng nhập nội dung!</div>
                        </div>
                        <button class="btn btn-primary w-100 text-uppercase" type="submit">Gửi liên hệ</button>
                        <div class="customer-option mt-4 text-center">
                            <a href="/" class="btn-text">Quay lại trang chủ</a>
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
        const form = document.getElementById('contact-form');
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