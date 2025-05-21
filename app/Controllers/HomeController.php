<?php
namespace App\Controllers;

use App\Models\Product;

class HomeController {
    private $productModel;

    public function __construct() {
        $this->productModel = new Product();
    }

    public function index() {
        $hotDeals = $this->productModel->getHotDeals();
        $products = $this->productModel->getAll();
        require_once __DIR__ . '/../Views/home/index.php';
    }
}