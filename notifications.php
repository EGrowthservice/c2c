<?php
use App\Helpers\Session;

Session::start();
$currentUserId = Session::get('user')['id'] ?? null;

if (!$currentUserId) {
    Session::set('error', 'Vui lòng đăng nhập để nhận thông báo!');
    header('Location: /login');
    exit;
}
?>

<!-- Đặt trong header.php hoặc một file riêng -->
<div id="notification-container" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

<style>
    .notification-toast {
        min-width: 300px;
        max-width: 400px;
        padding: 1rem;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        margin-bottom: 1rem;
        transition: all 0.3s ease-in-out;
        opacity: 0;
        transform: translateX(100%);
    }
    .notification-toast.show {
        opacity: 1;
        transform: translateX(0);
    }
    .notification-toast.message {
        background-color: #e7f3ff;
        border-left: 5px solid #007bff;
    }
    .notification-toast.order {
        background-color: #e6ffed;
        border-left: 5px solid #28a745;
    }
    .notification-toast.review {
        background-color: #fff3e0;
        border-left: 5px solid #ffc107;
    }
    .notification-toast.report {
        background-color: #f8d7da;
        border-left: 5px solid #dc3545;
    }
    .notification-icon {
        font-size: 1.5rem;
        margin-right: 0.75rem;
    }
    .notification-title {
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 0.25rem;
    }
    .notification-body {
        font-size: 0.9rem;
        color: #333;
    }
    .notification-close {
        position: absolute;
        top: 10px;
        right: 10px;
        cursor: pointer;
        color: #666;
    }
    @media (max-width: 576px) {
        .notification-toast {
            min-width: 90%;
        }
    }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        const userId = <?php echo json_encode($currentUserId); ?>;
        let ws = new WebSocket('ws://localhost:9000?user_id=' + userId);

        // Sound for notifications
        const notificationSound = new Audio('/assets/sounds/notification.mp3');

        // Function to show notification
        function showNotification(type, title, message, link = '#') {
            const toastId = 'toast-' + Date.now();
            const iconMap = {
                message: 'fas fa-envelope',
                order: 'fas fa-shopping-cart',
                review: 'fas fa-star',
                report: 'fas fa-exclamation-triangle'
            };
            const toastHtml = `
                <div id="${toastId}" class="notification-toast ${type} show">
                    <i class="${iconMap[type]} notification-icon"></i>
                    <div>
                        <div class="notification-title">${title}</div>
                        <div class="notification-body">${message}</div>
                    </div>
                    <i class="fas fa-times notification-close" onclick="$('#${toastId}').remove();"></i>
                </div>
            `;
            $('#notification-container').append(toastHtml);

            // Play sound
            notificationSound.play().catch(err => console.log('Audio error:', err));

            // Auto-remove after 5 seconds
            setTimeout(() => {
                $(`#${toastId}`).removeClass('show').css('transform', 'translateX(100%)');
                setTimeout(() => $(`#${toastId}`).remove(), 300);
            }, 5000);
        }

        ws.onopen = function() {
            console.log('WebSocket connection established at <?php echo date('h:i A T, l, F d, Y'); ?>');
        };

        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            let title, message, type;

            switch (data.type) {
                case 'message':
                    title = 'Tin nhắn mới';
                    message = `Bạn nhận được tin nhắn từ ${data.sender_name}: "${data.message}"`;
                    type = 'message';
                    break;
                case 'order':
                    title = 'Cập nhật đơn hàng';
                    message = `Đơn hàng #${data.order_id} đã được cập nhật trạng thái: ${data.status}`;
                    type = 'order';
                    break;
                case 'review':
                    title = 'Đánh giá mới';
                    message = `Sản phẩm "${data.product_name}" nhận được đánh giá ${data.rating} sao từ ${data.reviewer_name}.`;
                    type = 'review';
                    break;
                case 'report':
                    title = 'Báo cáo mới';
                    message = `Sản phẩm "${data.product_name}" bị báo cáo bởi ${data.reporter_name}: "${data.reason}"`;
                    type = 'report';
                    break;
                default:
                    return;
            }

            showNotification(type, title, message, data.link || '#');
        };

        ws.onclose = function() {
            console.log('WebSocket connection closed');
        };

        ws.onerror = function(error) {
            console.error('WebSocket error:', error);
        };

        $('#notification-container').on('click', '.notification-toast', function() {
            window.location.href = $(this).data('link') || '#';
        });
    });
</script>