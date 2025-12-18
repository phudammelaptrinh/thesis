<?php
session_start();
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../Models/UserModel.php';
require_once __DIR__ . '/../Models/DatabaseModel.php';


class AuthController
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            $user = $this->userModel->findByEmail($email);

            // ✅ Kiểm tra mật khẩu mã hóa
            if ($user && password_verify($password, $user['password'])) {

                if ($user['status'] !== 'active') {
                    $_SESSION['error'] = 'Tài khoản không hoạt động.';
                    header('Location: ' . url('apps/Views/auth/login.php'));
                    exit;
                }

                // ✅ Lưu session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $this->userModel->getRole($user['user_id']);
                $_SESSION['status'] = $user['status'];

                // ✅ Điều hướng theo vai trò
                switch ($_SESSION['role']) {
                    case 'Admin':
                        header('Location: ' . url('apps/Views/admin/index.php?page=generals'));
                        exit;
                    case 'Giao việc':
                        header('Location: ' . url('apps/Views/ngv/index.php?page=projects'));
                        exit;
                    default:
                        header('Location: ' . url('apps/Views/nnv/index.php?page=task_receive'));
                        exit;
                }
            } else {
                $_SESSION['error'] = 'Email hoặc mật khẩu không đúng.';
                header('Location: ' . url('apps/Views/auth/login.php'));
                exit;
            }
        }
    }

    public function logout()
    {
        session_unset();
        session_destroy();
        header('Location: ' . url('apps/Views/auth/login.php'));
        exit;
    }

    // 🔹 ✅ Hàm đổi mật khẩu (dùng cho AJAX)
    public function changePassword()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập lại.']);
            return;
        }

        $user_id = $_SESSION['user_id'];
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $confirm = $_POST['confirm_password'] ?? '';

        if ($new !== $confirm) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu xác nhận không khớp.']);
            exit;
        }

        $user = $this->userModel->getUserById($user_id);
        if (!$user) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy người dùng.']);
            exit;
        }

        // ✅ Kiểm tra mật khẩu cũ (dùng password_verify)
        if (!password_verify($current, $user['password'])) {
            echo json_encode(['success' => false, 'message' => 'Mật khẩu hiện tại không đúng.']);
            exit;
        }

        // ✅ Mã hóa mật khẩu mới
        $hashedNew = password_hash($new, PASSWORD_DEFAULT);

        $result = $this->userModel->changePassword($user_id, $hashedNew);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Đổi mật khẩu thành công!']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể đổi mật khẩu.']);
            exit;
        }
    }

    // 🔹 ✅ Hàm cập nhật hồ sơ cá nhân (dùng cho AJAX)
    public function updateProfile()
    {
        header('Content-Type: application/json; charset=utf-8');
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng đăng nhập lại.']);
            return;
        }

        $user_id = $_POST['user_id'] ?? 0;
        $full_name = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');

        // Kiểm tra quyền (chỉ được sửa profile của mình)
        if ($user_id != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền sửa hồ sơ này.']);
            exit;
        }

        if (empty($full_name) || empty($email)) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin.']);
            exit;
        }

        // Kiểm tra email đã tồn tại (trừ email của chính mình)
        $existingUser = $this->userModel->findByEmail($email);
        if ($existingUser && $existingUser['user_id'] != $user_id) {
            echo json_encode(['success' => false, 'message' => 'Email đã được sử dụng bởi người khác.']);
            exit;
        }

        $result = $this->userModel->updateProfile($user_id, $full_name, $email);
        if ($result) {
            // Cập nhật session
            $_SESSION['full_name'] = $full_name;
            echo json_encode(['success' => true, 'message' => 'Cập nhật hồ sơ thành công!']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật hồ sơ.']);
            exit;
        }
    }
}

$auth = new AuthController();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    switch ($action) {
        case 'login':
            $auth->login();
            exit;

        case 'changePassword':
            $auth->changePassword();
            exit;

        case 'updateProfile':
            $auth->updateProfile();
            exit;

        // default:
        //     echo json_encode(['success' => false, 'message' => 'Trang không tồn tại']);
        //     break;
    }
} elseif (isset($_GET['action']) && $_GET['action'] === 'logout') {
    $auth->logout();
}
