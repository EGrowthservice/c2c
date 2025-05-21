<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container mt-5">
    <h2>Xác nhận đặt hàng</h2>
    <p>Sản phẩm: <?= htmlspecialchars($product['title']) ?></p>
    <p>Giá: <?= htmlspecialchars($product['price']) ?> VND</p>
    <form method="POST" action="/orders/create/<?= $product['id'] ?>">
        <button type="submit" class="btn btn-primary">Xác nhận mua</button>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>