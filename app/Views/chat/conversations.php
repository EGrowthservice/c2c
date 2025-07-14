<?php
use App\Helpers\Session;

$currentUserId = Session::get('user')['id'] ?? null;

if (!$currentUserId) {
    Session::set('error', 'Vui lòng đăng nhập!');
    header('Location: /login');
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách cuộc trò chuyện</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .conversations-section {
            max-width: 800px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }
        .list-group-item {
            transition: background-color 0.2s;
            border-radius: 8px;
            margin-bottom: 0.5rem;
        }
        .list-group-item:hover {
            background-color: #f1f3f5;
        }
        .conversation-title {
            font-weight: 600;
            color: #1a1a1a;
        }
        .product-id {
            color: #6c757d;
        }
        .no-conversations {
            text-align: center;
            padding: 2rem;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <?php include __DIR__ . '/../layouts/header.php'; ?>
    <?php include __DIR__ . '/../products/linkcss.php'; ?>
    <main class="pt-90">
  <div class="mb-4 pb-4"></div>
        <div class="container conversations-section">
            <h4 class="fw-bold mb-4 text-center text-primary">Danh sách cuộc trò chuyện</h4>
            <div class="list-group shadow-sm">
                <?php if (!empty($conversations)): ?>
                    <?php foreach ($conversations as $conv): ?>
                        <a href="/chat/<?= htmlspecialchars($conv['product_id']) ?>/<?= htmlspecialchars($conv['other_user_id']) ?>" 
                           class="list-group-item list-group-item-action border-0">
                            <div class="d-flex w-100 justify-content-between align-items-center">
                                <div>
                                    <h5 class="mb-1 conversation-title">
                                        <?= htmlspecialchars($conv['other_user_name'] ?? 'Người dùng không xác định') ?>
                                    </h5>
                                    <p class="mb-0 product-id">Sản phẩm: #<?= htmlspecialchars($conv['product_id']) ?></p>
                                </div>
                                <small class="text-muted"><?= htmlspecialchars($conv['last_message_time']) ?></small>
                            </div>
                        </a>
                        <hr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="no-conversations">
                        <p class="text-muted mb-0">Chưa có cuộc trò chuyện nào.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include __DIR__ . '/../layouts/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>