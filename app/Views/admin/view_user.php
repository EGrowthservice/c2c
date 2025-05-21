<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông tin người dùng</title>
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
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Thông tin người dùng</h2>
                    <a href="/admin/reports" class="btn btn-outline-primary">Quay lại báo cáo</a>
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
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Chi tiết người dùng</h5>
                        <p><strong>ID:</strong> <?php echo htmlspecialchars($user['id']); ?></p>
                        <p><strong>Tên người dùng:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Trạng thái:</strong> <?php echo htmlspecialchars($user['is_active'] == 0 ? 'Hoạt động' : 'Bị khóa'); ?></p>
                        <p><strong>Vai trò:</strong> <?php echo htmlspecialchars($user['role'] ?? 'user'); ?></p>
                        <p><strong>Ngày tạo:</strong> <?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($user['created_at']))); ?></p>
                    </div>
                </div>
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title">Báo cáo liên quan</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Lý do</th>
                                        <th scope="col">Ngày báo cáo</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($reports)): ?>
                                        <tr>
                                            <td colspan="4" class="text-center">Không có báo cáo nào!</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($reports as $report): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($product['id']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($report['reason'], 0, 50)); ?>...</td>
                                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($report['created_at']))); ?></td>
                                                <td>
                                                    <a href="/admin/reports/delete/<?php echo $report['id']; ?>" class="btn btn-sm btn-danger delete-report" data-id="<?php echo $report['id']; ?>">Xóa</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Sản phẩm đã đăng</h5>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th scope="col">ID</th>
                                        <th scope="col">Tiêu đề</th>
                                        <th scope="col">Danh mục</th>
                                        <th scope="col">Giá</th>
                                        <th scope="col">Trạng thái</th>
                                        <th scope="col">Ngày đăng</th>
                                        <th scope="col">Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($products)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">Không có sản phẩm nào!</td>
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
                                                <td><?php echo htmlspecialchars(substr($product['title'], 0, 50)); ?>...</td>
                                                <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                                                <td><?php echo htmlspecialchars(number_format($product['price'], 0, ',', '.')); ?> VNĐ</td>
                                                <td><?php echo htmlspecialchars($statusMap[$product['status']] ?? $product['status']); ?></td>
                                                <td><?php echo htmlspecialchars(date('d/m/Y H:i', strtotime($product['created_at']))); ?></td>
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
            document.querySelectorAll('.delete-report').forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const reportId = this.getAttribute('data-id');
                    Swal.fire({
                        title: 'Xác nhận xóa',
                        text: 'Bạn có chắc muốn xóa báo cáo này?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Xóa',
                        cancelButtonText: 'Hủy'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch(`/admin/reports/delete/${reportId}`, {
                                    method: 'POST',
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
                                        text: 'Đã xảy ra lỗi khi xóa báo cáo!',
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