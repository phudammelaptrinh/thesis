<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../config/config.php';
include('../layouts/header.php');
?>
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">

<div class="login-container">
    <div class="login-box">
        <h2>Đăng nhập vào Taskbb</h2>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"
                style="background:#f8d7da;color:#721c24;padding:10px;border-radius:5px;margin-bottom:15px;">
                <?= htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"
                style="background:#d4edda;color:#155724;padding:10px;border-radius:5px;margin-bottom:15px;">
                <?= htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <p class="subtitle">Quản lý và giao việc hiệu quả cho nhóm của bạn</p>

        <form method="POST" action="../../Controllers/AuthController.php" id="loginForm">
            <input type="hidden" name="action" value="login">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required placeholder="Nhập email của bạn">
            </div>

            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <input type="password" id="password" name="password" required placeholder="Nhập mật khẩu">
            </div>

            <button type="submit" class="btn-gradient w-full">Đăng nhập</button>
        </form>
    </div>
</div>

</body>

</html>