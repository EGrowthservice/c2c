<?php

namespace App\Controllers;

use App\Models\Contact;
use App\Helpers\Session;

class ContactController
{
    private $contactModel;

    public function __construct()
    {
        $this->contactModel = new Contact();
    }

    public function index()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';

            if (empty($name) || empty($email) || empty($subject) || empty($message)) {
                $response = ['success' => false, 'message' => 'Vui lòng điền đầy đủ các trường!'];
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $response = ['success' => false, 'message' => 'Email không hợp lệ!'];
            } else {
                if ($this->contactModel->saveContact($name, $email, $subject, $message)) {
                    $response = ['success' => true, 'message' => 'Gửi liên hệ thành công!', 'redirect' => '/contact'];
                } else {
                    $response = ['success' => false, 'message' => 'Gửi liên hệ thất bại!'];
                }
            }

            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode($response);
                exit;
            }

            Session::set($response['success'] ? 'success' : 'error', $response['message']);
            if ($response['success']) {
                header('Location: /contact');
                exit;
            }
        }

        require_once __DIR__ . '/../Views/contact/index.php';
    }
}
