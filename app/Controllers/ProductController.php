<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Helpers\Session;

class ProductController
{
    private $productModel;
    private $categoryModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->categoryModel = new Category();
    }

    public function index()
    {
        $filter = $_GET['sort'] ?? 'latest';
        $keyword = $_GET['keyword'] ?? '';
        $sort = $_GET['sort'] ?? 'latest';
        $page = max((int) ($_GET['page'] ?? 1), 1);

        $limit = 12;
        $offset = ($page - 1) * $limit;

        // Lấy sản phẩm + tổng số
        $products = $this->productModel->getAll($sort, $keyword, $limit, $offset);
        $totalProducts = $this->productModel->countAll($keyword);
        $totalPages = ceil($totalProducts / $limit);


        if (!in_array($filter, ['latest', 'featured', 'popular'])) {
            $filter = 'latest';
        }

        $products = $this->productModel->getAll($filter, $keyword);

        require_once __DIR__ . '/../Views/products/index.php';
    }

    public function show($id)
    {
        $product = $this->productModel->find($id);
        if (!$product) {
            Session::set('error', 'Sản phẩm không tồn tại!');
            header('Location: /products');
            exit;
        }
        $this->productModel->incrementViews($id);
        require_once __DIR__ . '/../Views/products/show.php';
    }

    public function create()
    {
        if (!Session::get('user')) {
            Session::set('error', 'Vui lòng đăng nhập để đăng sản phẩm!');
            header('Location: /login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $categoryId = $_POST['category_id'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $userId = Session::get('user')['id'];
            $seller_id = Session::get('user')['id'];

            // Validate
            if (empty($title)) {
                Session::set('error', 'Vui lòng nhập tiêu đề sản phẩm!');
                header('Location: /products/create');
                exit;
            }
            if (empty($categoryId)) {
                Session::set('error', 'Vui lòng chọn danh mục!');
                header('Location: /products/create');
                exit;
            }
            if (empty($description)) {
                Session::set('error', 'Vui lòng nhập mô tả sản phẩm!');
                header('Location: /products/create');
                exit;
            }
            if ($price <= 0) {
                Session::set('error', 'Vui lòng nhập giá hợp lệ (lớn hơn 0)!');
                header('Location: /products/create');
                exit;
            }

            $image = $this->handleImageUpload();
            if (!$image && !empty($_FILES['image']['name'])) {
                Session::set('error', 'Tải hình ảnh thất bại!');
                header('Location: /products/create');
                exit;
            }

            if ($this->productModel->create($userId, $seller_id, $title, $description, $price, $image, $categoryId)) {
                Session::set('success', 'Đăng sản phẩm thành công! Đang chờ duyệt.');
                header('Location: /profile/products');
                exit;
            } else {
                Session::set('error', 'Đăng sản phẩm thất bại!');
            }
        }
        $categories = $this->categoryModel->getAll();
        require_once __DIR__ . '/../Views/products/create.php';
    }

    public function edit($id)
    {
        if (!Session::get('user')) {
            Session::set('error', 'Vui lòng đăng nhập để chỉnh sửa!');
            header('Location: /login');
            exit;
        }
        $product = $this->productModel->find($id);
        if (!$product || $product['user_id'] != Session::get('user')['id']) {
            Session::set('error', 'Bạn không có quyền chỉnh sửa sản phẩm này!');
            header('Location: /profile/products');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $title = trim($_POST['title'] ?? '');
            $categoryId = $_POST['category_id'] ?? '';
            $description = trim($_POST['description'] ?? '');
            $price = floatval($_POST['price'] ?? 0);

            // Validate
            if (empty($title)) {
                Session::set('error', 'Vui lòng nhập tiêu đề sản phẩm!');
                header('Location: /products/edit/' . $id);
                exit;
            }
            if (empty($categoryId)) {
                Session::set('error', 'Vui lòng chọn danh mục!');
                header('Location: /products/edit/' . $id);
                exit;
            }
            if (empty($description)) {
                Session::set('error', 'Vui lòng nhập mô tả sản phẩm!');
                header('Location: /products/edit/' . $id);
                exit;
            }
            if ($price <= 0) {
                Session::set('error', 'Vui lòng nhập giá hợp lệ (lớn hơn 0)!');
                header('Location: /products/edit/' . $id);
                exit;
            }

            $image = $this->handleImageUpload() ?? $product['image'];
            if ($this->productModel->update($id, $title, $description, $price, $image, $categoryId)) {
                Session::set('success', 'Chỉnh sửa thành công! Đang chờ duyệt.');
                header('Location: /profile/products');
                exit;
            } else {
                Session::set('error', 'Chỉnh sửa thất bại!');
            }
        }
        $categories = $this->categoryModel->getAll();
        require_once __DIR__ . '/../Views/products/edit.php';
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $product = $this->productModel->find($id);
            if (!$product || $product['user_id'] != Session::get('user')['id']) {
                $response = ['success' => false, 'message' => 'Bạn không có quyền xóa sản phẩm này!'];
            } elseif ($this->productModel->delete($id)) {
                $response = ['success' => true, 'message' => 'Xóa sản phẩm thành công!'];
            } else {
                $response = ['success' => false, 'message' => 'Xóa sản phẩm thất bại!'];
            }
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
        Session::set('error', 'Yêu cầu không hợp lệ!');
        header('Location: /profile/products');
        exit;
    }

    private function handleImageUpload()
    {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $targetDir = __DIR__ . '/../../public/Uploads/';
            $fileName = uniqid() . '_' . basename($_FILES['image']['name']);
            $targetFile = $targetDir . $fileName;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
                return $fileName;
            }
        }
        return null;
    }
}
