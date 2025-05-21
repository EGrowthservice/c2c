<?php include __DIR__ . '/../layouts/header.php'; ?>
<div class="container mt-5">
    <h2>Thanh toán</h2>
    <form method="POST" action="/payment/process/<?= $orderId ?>">
        <div class="mb-3">
            <label class="form-label">Số tiền</label>
            <input type="number" name="amount" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Phương thức thanh toán</label>
            <select name="payment_method" class="form-control" required>
                <option value="bank_transfer">Chuyển khoản ngân hàng</option>
                <option value="cash">Tiền mặt</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Thanh toán</button>
    </form>
</div>
<?php include __DIR__ . '/../layouts/footer.php'; ?>