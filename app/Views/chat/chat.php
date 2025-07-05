<?php

use App\Helpers\Session;
use App\Models\Product;

$currentUserId = Session::get('user')['id'] ?? null;
$productId = $product_id ?? null; // Lấy từ tham số route
$sellerId = $seller_id ?? null;   // Lấy từ tham số route
$productModel = new Product();
$product = $productModel->find($productId);
$product_name = $product['title'] ?? 'Không rõ tên sản phẩm';
include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../products/linkcss.php';
if (!$currentUserId || !$productId || !$sellerId) {
    Session::set('error', 'Vui lòng đăng nhập và kiểm tra thông tin!');
    header('Location: /login');
    exit;
}
?>

<main class="pt-5">
    <div class="container">
        <div class="mb-5"></div>
        <section class="chat-section">
            <h4 class="fw-bold mb-3">Chat với người bán - Tên Sản phẩm: <?= htmlspecialchars($product_name) ?></h4>
            <div id="chat-messages" class="mb-3" style="height: 400px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background-color: #f8f9fa;">
                <?php
                $chatModel = new \App\Models\ChatModel();
                $chatHistory = $chatModel->getChats($productId, $currentUserId, $sellerId);
                foreach ($chatHistory as $msg) {
                    $isSender = $msg['sender_id'] == $currentUserId;
                    $senderLabel = $isSender ? 'Bạn' : 'Người bán';
                    $class = $isSender ? 'user' : 'seller';

                    echo '<div class="message ' . $class . '">';
                    echo '<strong>' . $senderLabel . ':</strong> ' . htmlspecialchars($msg['message']);
                    echo '<small>' . $msg['created_at'] . '</small>';
                    echo '</div>';
                }
                ?>
            </div>
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control" placeholder="Nhập tin nhắn...">
                <button class="btn btn-primary" id="send-message">Gửi</button>
            </div>
        </section>
    </div>
</main>
<style>
    .chat-section {
        max-width: 800px;
        margin: 0 auto;
    }

    #chat-messages {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .message {
        max-width: 70%;
        padding: 10px 14px;
        border-radius: 20px;
        position: relative;
        word-wrap: break-word;
        display: inline-block;
        font-size: 14px;
        line-height: 1.4;
    }

    .message small {
        display: block;
        font-size: 11px;
        margin-top: 5px;
        color: #666;
    }

    .message.user {
        align-self: flex-end;
        background-color: rgb(141, 150, 163);
        color: #fff;
        border-bottom-right-radius: 0;
    }

    .message.seller {
        align-self: flex-start;
        background-color: #f1f0f0;
        color: #000;
        border-bottom-left-radius: 0;
    }
</style>

<?php
include __DIR__ . '/../layouts/footer.php';
?>

<script src="/assets/js/plugins/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $(document).ready(function() {
        const productId = <?= json_encode($productId) ?>;
        const userId = <?= json_encode($currentUserId) ?>;
        const sellerId = <?= json_encode($sellerId) ?>;
        let ws = new WebSocket('ws://localhost:9000?user_id=' + userId + '&product_id=' + productId + '&seller_id=' + sellerId);

        // Function to scroll to the bottom of the chat
        function scrollToBottom() {
            const chatMessages = $('#chat-messages');
            chatMessages.scrollTop(chatMessages[0].scrollHeight);
        }

        // Scroll to bottom on page load to show the latest message
        scrollToBottom();

        ws.onopen = function() {
            console.log('WebSocket connection established at <?= date('h:i A T, l, F d, Y') ?>'); // 11:43 PM +07, Saturday, July 05, 2025
        };

        ws.onmessage = function(event) {
            const data = JSON.parse(event.data);
            $('#chat-messages').append(
                '<div class="message ' + (data.user_id == userId ? 'user' : 'seller') + '">' +
                '<strong>' + (data.user_id == userId ? 'Bạn' : 'Người bán') + ':</strong> ' +
                data.message +
                '<small>' + data.timestamp + '</small>' +
                '</div>'
            );
            scrollToBottom();
        };

        ws.onclose = function() {
            console.log('WebSocket connection closed');
        };

        ws.onerror = function(error) {
            console.error('WebSocket error:', error);
        };

        $('#send-message').on('click', function() {
            const message = $('#chat-input').val().trim();
            if (message && ws.readyState === WebSocket.OPEN) {
                $.ajax({
                    url: '/chat/save',
                    method: 'POST',
                    data: {
                        sender_id: userId,
                        receiver_id: sellerId,
                        product_id: productId,
                        message: message
                    },
                    success: function(response) {
                        if (response.success) {
                            // Thêm tin nhắn vào giao diện ngay lập tức
                            $('#chat-messages').append(
                                '<div class="message user">' +
                                '<strong>Bạn:</strong> ' + message +
                                '<small>' + response.timestamp + '</small>' +
                                '</div>'
                            );
                            scrollToBottom();
                            // Gửi tin nhắn qua WebSocket cho người nhận
                            ws.send(JSON.stringify({
                                message: message,
                                user_id: userId,
                                timestamp: response.timestamp,
                                target_user_id: sellerId
                            }));
                            $('#chat-input').val('');
                        } else {
                            alert('Gửi tin nhắn thất bại: ' + (response.message || 'Lỗi không xác định'));
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX error:', xhr.responseText, status, error);
                        alert('Lỗi kết nối server');
                    }
                });
            }
        });

        $('#chat-input').on('keypress', function(e) {
            if (e.which === 13 && $(this).val().trim()) {
                $('#send-message').click();
            }
        });
    });
</script>