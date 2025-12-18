<?php
// Hiển thị trang landing page
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../apps/Controllers/HomeController.php';

$controller = new HomeController();
$controller->dashboard();
?>