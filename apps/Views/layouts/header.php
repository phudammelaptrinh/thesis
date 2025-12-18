<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Taskbb - Sự ưu tiên của bạn</title>
    <link rel="icon" type="image/png" href="<?= asset('logo/logo.png') ?>">
    <script>
        window.BASE_URL = '<?= BASE_URL ?>';
        window.APP_BASE_URL = '<?= BASE_URL ?>'; // Alias
    </script>
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</head>

<body>
    <header class="header">
        <div class="container">
            <div class="header-left">
                <div class="logo">
                    <div class="logo-icon">
                        <a href="<?= url('public/') ?>">
                            <img src="<?= asset('logo/logo.png') ?>" alt="Taskbb Logo"
                                style="height: 40px; vertical-align: middle; margin-right: 10px;">
                            <span class="logo-text">Taskbb | Ứng dụng cho mọi công việc</span>
                        </a>
                    </div>
                </div>
            </div>

            <div class="header-right">
                <a href="#" class="btn-outline">Liên hệ bán hàng</a>
                <a href="<?= url('apps/Views/auth/login.php') ?>" class="btn-outline">Đăng nhập</a>
                <a href="#" class="btn-gradient">Đăng ký</a>
            </div>
        </div>
    </header>