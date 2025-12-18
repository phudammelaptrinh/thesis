<?php
/**
 * User AJAX Handler
 * Xử lý các request AJAX liên quan đến User
 */

// Bật error reporting để debug (tắt khi production)
error_reporting(E_ALL);
ini_set('display_errors', 0); // Không hiển thị lỗi HTML, chỉ trả JSON

// Set header JSON
header('Content-Type: application/json; charset=utf-8');

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Bạn chưa đăng nhập!'
    ]);
    exit;
}

// Load controller
require_once __DIR__ . '/../Controllers/UserController.php';

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? null;

    if (!$action) {
        throw new Exception('Không xác định được action');
    }

    $controller = new UserController();

    switch ($action) {
        case 'updateProfile':
            // Lấy dữ liệu từ POST
            $user_id = $_SESSION['user_id'];
            $full_name = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');

            // Validate
            if (empty($full_name)) {
                throw new Exception('Họ và tên không được để trống');
            }

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email không hợp lệ');
            }

            // Gọi model update
            require_once __DIR__ . '/../Models/UserModel.php';
            $userModel = new UserModel();
            $success = $userModel->updateProfile($user_id, $full_name, $email);

            if ($success) {
                // Cập nhật lại session
                $_SESSION['full_name'] = $full_name;

                echo json_encode([
                    'success' => true,
                    'message' => 'Cập nhật hồ sơ thành công!'
                ]);
            } else {
                throw new Exception('Không thể cập nhật hồ sơ. Email có thể đã tồn tại.');
            }
            break;

        default:
            throw new Exception('Action không hợp lệ: ' . $action);
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} catch (Throwable $e) {
    // Bắt mọi lỗi khác
    echo json_encode([
        'success' => false,
        'message' => 'Đã xảy ra lỗi hệ thống: ' . $e->getMessage()
    ]);
}
