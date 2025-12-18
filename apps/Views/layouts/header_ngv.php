<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Controllers/UserController.php';
$user = new UserController();

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $currentUser = $user->getUserById($_SESSION['user_id']);
}

$userName = $currentUser['full_name'] ?? 'Người giao việc';
?>

<!-- 🔹 Thanh điều hướng -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">


        <div class="d-flex align-items-center ms-auto">
            <!-- 👤 Thông tin người dùng -->
            <span class="text-light me-3">
                👋 Xin chào, <strong><?= htmlspecialchars($userName) ?></strong>
            </span>
        </div>
    </div>
</nav>

<!-- Bootstrap -->
<script>
    window.BASE_URL = '<?= BASE_URL ?>';
    window.APP_BASE_URL = '<?= BASE_URL ?>'; // Alias
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>