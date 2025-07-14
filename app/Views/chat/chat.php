<?php
use App\Helpers\Session;
use App\Models\Product;

$currentUserId = Session::get('user')['id'] ?? null;
$productId = $product_id ?? null; // Lấy từ tham số route
$sellerId = $seller_id ?? null;   // Lấy từ tham số route
$productModel = new Product();
$product = $productModel->find($productId);
$product_name = $product['title'] ?? 'Không rõ tên sản phẩm';

if (!$currentUserId || !$productId || !$sellerId) {
    Session::set('error', 'Vui lòng đăng nhập và kiểm tra thông tin!');
    header('Location: /login');
    exit;
}
?>

    <title>Chat với người bán - <?= htmlspecialchars($product_name) ?></title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
   <style>
 

    .chat-section {
        max-width: 800px;
        margin: auto;
        padding: 1.5rem;
        background-color: #fff;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.05);
    }

    .chat-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 1rem;
        border-bottom: 1px solid #e0e0e0;
    }

    .chat-header h4 {
        margin: 0;
        font-weight: 600;
    }

    #chat-messages {
        height: 450px;
        overflow-y: auto;
        padding: 1rem;
        background-color: #f9f9fb;
        border-radius: 10px;
        margin-bottom: 1rem;
        display: flex;
        flex-direction: column;
    }

    .message {
        max-width: 75%;
        padding: 0.75rem 1rem;
        margin: 0.4rem 0;
        border-radius: 20px;
        font-size: 0.95rem;
        line-height: 1.4;
        position: relative;
        display: inline-block;
        word-wrap: break-word;
    }

    .message small {
        display: block;
        margin-top: 5px;
        font-size: 0.75rem;
        color: black;
    }

    .message.user {
        align-self: flex-end;
        background-color: #007bff;
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message.seller {
        align-self: flex-start;
        background-color: #e4e6eb;
        color: #000;
        border-bottom-left-radius: 4px;
    }

    .input-area {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    #chat-input {
        flex: 1;
        border-radius: 20px;
        padding: 0.6rem 1rem;
        border: 1px solid #ccc;
    }

    #send-message {
        border-radius: 30%;
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #007bff;
        color: white;
        border: none;
    }

    #send-message i {
        font-size: 18px;
    }

    @media (max-width: 576px) {
        .chat-section {
            padding: 1rem;
            border-radius: 0;
        }

        #chat-messages {
            height: 350px;
        }

        .message {
            max-width: 85%;
        }

        #send-message {
            width: 40px;
            height: 40px;
        }
    }
</style>

</head>
    <?php include __DIR__ . '/../layouts/header.php'; ?>
    <?php include __DIR__ . '/../products/linkcss.php'; ?>

    <main class="pt-5">
        <div class="container chat-section">
            <div class="chat-header">
                <h4 class="fw-bold mb-0">Chat với người bán - <span class="fst-italic"><?= htmlspecialchars($product_name) ?></span></h4>
            </div>
            <div id="chat-messages" class="mb-3">
                <?php
                $chatModel = new \App\Models\ChatModel();
                $chatHistory = $chatModel->getChats($productId, $currentUserId, $sellerId);
                foreach ($chatHistory as $msg) {
                    $isSender = $msg['sender_id'] == $currentUserId;
                    $senderLabel = $isSender ? 'Bạn' : 'Người bán';
                    $class = $isSender ? 'user' : 'seller';
                    ?>
                    <div class="message <?php echo $class; ?>">
                        <strong><?php echo $senderLabel; ?>:</strong> <?php echo htmlspecialchars($msg['message']); ?>
                        <small><?php echo $msg['created_at']; ?></small>
                    </div>
                    <?php
                }
                ?>
            </div>
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control" placeholder="Nhập tin nhắn...">
                <button class="btn btn-primary" id="send-message">
                    <i class="fas fa-paper-plane me-1"></i> Gửi
                </button>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            const productId = <?php echo json_encode($productId); ?>;
            const userId = <?php echo json_encode($currentUserId); ?>;
            const sellerId = <?php echo json_encode($sellerId); ?>;
            let ws = new WebSocket('ws://localhost:9000?user_id=' + userId + '&product_id=' + productId + '&seller_id=' + sellerId);

            // Function to scroll to the bottom of the chat
            function scrollToBottom() {
                const chatMessages = $('#chat-messages');
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }

            // Scroll to bottom on page load to show the latest message
            scrollToBottom();

            ws.onopen = function() {
                console.log('WebSocket connection established at <?php echo date('h:i A T, l, F d, Y'); ?>');
            };

            ws.onmessage = function(event) {
                const data = JSON.parse(event.data);
                $('#chat-messages').append(
                    `<div class="message ${data.user_id == userId ? 'user' : 'seller'}">
                        <strong>${data.user_id == userId ? 'Bạn' : 'Người bán'}:</strong> ${data.message}
                        <small>${data.timestamp}</small>
                    </div>`
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
                                $('#chat-messages').append(
                                    `<div class="message user">
                                        <strong>Bạn:</strong> ${message}
                                        <small>${response.timestamp}</small>
                                    </div>`
                                );
                                scrollToBottom();
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