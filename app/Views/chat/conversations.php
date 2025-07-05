<?php

use App\Helpers\Session;

$currentUserId = Session::get('user')['id'] ?? null;

if (!$currentUserId) {
    Session::set('error', 'Vui lòng đăng nhập!');
    header('Location: /login');
    exit;
}
// include __DIR__ . '/../layouts/header.php';
include __DIR__ . '/../products/linkcss.php';
?>


<main class="pt-5">
    <div class="container">
        <div class="mb-5"></div>
        <section class="conversations-section">
            <h4 class="fw-bold mb-3">Danh sách cuộc trò chuyện</h4>
            <div class="list-group">
                <?php foreach ($conversations as $conv): ?>
                    <a href="/chat/<?= $conv['product_id'] ?>/<?= $conv['other_user_id'] ?>" class="list-group-item list-group-item-action">
                        <div class="d-flex w-100 justify-content-between">
                            <h5 class="mb-1"><?= htmlspecialchars($conv['other_user_name'] ?? 'Người dùng không xác định') ?></h5>
                            <small><?= htmlspecialchars($conv['last_message_time']) ?></small>
                        </div>
                        <p class="mb-1">Sản phẩm: #<?= htmlspecialchars($conv['product_id']) ?></p>
                    </a>
                <?php endforeach; ?>
                <?php if (empty($conversations)): ?>
                    <p class="text-muted">Chưa có cuộc trò chuyện nào.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
</main>

<style>
    .conversations-section {
        max-width: 600px;
        margin: 0 auto;
    }

    .list-group-item {
        border: 1px solid #ddd;
        margin-bottom: 10px;
        border-radius: 5px;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
    }
</style>

<?php
include __DIR__ . '/../layouts/footer.php';
?>