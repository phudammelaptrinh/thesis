<?php
// apps/Views/common.php - Include file này ở đầu mỗi View

// Load config nếu chưa có
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/config.php';
}

// Hàm kiểm tra đăng nhập
function requireLogin()
{
    if (!isset($_SESSION['user_id'])) {
        header("Location: " . url('apps/Views/auth/login.php'));
        exit;
    }
}

// Hàm kiểm tra quyền
function requireRole($roles)
{
    if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], (array) $roles)) {
        header("Location: " . url('apps/Views/auth/login.php'));
        exit;
    }
}

// Output base URL cho JavaScript
function outputBaseUrl()
{
    echo '<script>const PHP_BASE_URL = "' . BASE_URL . '";</script>';
}
?>