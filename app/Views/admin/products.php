<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
            color: #fff;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            padding: 10px 15px;
            display: block;
        }

        .sidebar a:hover {
            color: #fff;
            background-color: #495057;
        }

        .sidebar a.active {
            color: #fff;
            background-color: #007bff;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .table {
            background-color: #fff;
            border-radius: 10px;
            overflow: hidden;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        .btn-sm {
            padding: 0.25rem 0.5rem;
        }

        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }

            .table-responsive {
                font-size: 0.9rem;
            }
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php
        include __DIR__ . '/layouts/header.php';
        ?>
        <div class="flex-grow-1 p-4">
            <div class="container-fluid">
                <hr>
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Quản lý sản phẩm</h2>
                    <form class="d-flex" action="/admin/products/search" method="GET">
                        <input class="form-control me-2" type="search" name="keyword" placeholder="Tìm kiếm sản phẩm" value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
                        <button class="btn btn-outline-primary" type="submit">Tìm</button>
                    </form>
                </div>
                <?php if ($success = \App\Helpers\Session::get('success')): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Thành công',
                                text: '<?php echo htmlspecialchars($success); ?>',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                    <?php \App\Helpers\Session::unset('success'); ?>
                <?php endif; ?>
                <?php if ($error = \App\Helpers\Session::get('error')): ?>
                    <script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: '<?php echo htmlspecialchars($error); ?>',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>
                    <?php \App\Helpers\Session::unset('error'); ?>
                <?php endif; ?>
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tên sản phẩm</th>
                                        <th scope="col">Mô tả</th>
                                        <th scope="col">Trạng thái</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($products)): ?>
                                        <tr>
                                            <td colspan="5" class="text-center">Không có sản phẩm nào!</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php
                                        $statusMap = [
                                            'pending' => 'Đang chờ duyệt',
                                            'approved' => 'Đã duyệt',
                                            'rejected' => 'Bị từ chối'
                                        ];
                                        ?>
                                        <?php foreach ($products as $product): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                                <td><?php echo htmlspecialchars($product['title']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($product['description'], 0, 50)); ?>...</td>
                                                <td><?php echo htmlspecialchars($statusMap[$product['status']] ?? $product['status']); ?></td>
                                                <td>
                                                    <?php if ($product['status'] !== 'approved'): ?>
                                                        <a href="/admin/products/status/<?php echo $product['id']; ?>/approved" class="btn btn-sm btn-success update-status" data-id="<?php echo $product['id']; ?>" data-status="approved">Duyệt</a>
                                                    <?php endif; ?>
                                                    <?php if ($product['status'] !== 'rejected'): ?>
                                                        <a href="/admin/products/status/<?php echo $product['id']; ?>/rejected" class="btn btn-sm btn-danger update-status" data-id="<?php echo $product['id']; ?>" data-status="rejected">Từ chối</a>
                                                    <?php endif; ?>
                                                    <?php if ($product['status'] !== 'pending'): ?>
                                                        <a href="/admin/products/status/<?php echo $product['id']; ?>/pending" class="btn btn-sm btn-warning update-status" data-id="<?php echo $product['id']; ?>" data-status="pending">Chờ duyệt</a>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="/admin/products/view/<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">Xem chi tiết</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.update-status').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const productId = this.getAttribute('data-id');
                    const status = this.getAttribute('data-status');
                    const statusText = {
                        'approved': 'Duyệt',
                        'rejected': 'Từ chối',
                        'pending': 'Chờ duyệt'
                    } [status];

                    Swal.fire({
                        title: 'Xác nhận cập nhật',
                        text: `Bạn có chắc muốn đặt trạng thái sản phẩm này thành "${statusText}"?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Cập nhật',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/admin/products/status/${productId}/${status}`, {
                                    method: 'GET',
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
                                        confirmButtonText: 'OK'
                                    }).then(() => {
                                        if (data.success) {
                                            window.location.reload();
                                        }
                                    });
                                })
                                .catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Lỗi',
                                        text: 'Đã xảy ra lỗi khi cập nhật trạng thái!',
                                        confirmButtonText: 'OK'
                                    });
                                });
                        }
                    });
                });
            });
        });
    </script>
</body>

</html>