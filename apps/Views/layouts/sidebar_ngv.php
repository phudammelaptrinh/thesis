<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = $_GET['pages'] ?? 'dashboard';

require_once __DIR__ . '/../../Controllers/UserController.php';
$userCtrl = new UserController();

$currentUser = null;

// ✅ Lấy thông tin người dùng đăng nhập từ session
if (isset($_SESSION['user_id'])) {
    $currentUser = $userCtrl->getUserById($_SESSION['user_id']);
}

$userName = $currentUser['full_name'] ?? 'Người giao việc';
?>

<div id="sidebar" class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white position-fixed"
    style="width: 250px; height: 100vh;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fs-5 fw-bold text-white m-0"><i class="bi bi-kanban"></i> TASKBB-GIAO VIỆC</h4>
    </div>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li>
            <a href="index.php?page=projects"
                class="nav-link text-white <?= ($currentPage == 'projects') ? 'active bg-primary' : 'text-white' ?>">
                <i class="bi bi-folder2-open me-2"></i>
                <span class="nav-text">Dự án</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=tasks"
                class="nav-link text-white <?= ($currentPage == 'tasks') ? 'active bg-primary' : 'text-white' ?>">
                <i class="bi bi-list-task me-2"></i>
                <span class="nav-text">Giao công việc</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=review"
                class="nav-link text-white <?= ($currentPage == 'review') ? 'active bg-primary' : 'text-white' ?>">
                <i class="bi bi-card-checklist me-2"></i>
                <span class="nav-text">Đánh giá công việc</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=reports"
                class="nav-link text-white <?= ($currentPage == 'reports') ? 'active bg-primary' : 'text-white' ?>">
                <i class="bi bi-bar-chart-line me-2"></i>
                <span class="nav-text">Thống kê dự án</span>
            </a>
        </li>
        <hr>
        <li>
            <a href="index.php?page=tasks_assigned"
                class="nav-link text-white <?= ($currentPage == 'task_assigned') ? 'active bg-primary' : 'text-white' ?>">
                <i class="bi bi-card-checklist me-2"></i>
                <span class="nav-text">Theo dõi công việc</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=history"
                class="nav-link text-white <?= ($currentPage == 'history') ? 'active bg-primary' : 'text-white' ?>">
                <i class="bi bi-clock-history me-2"></i>
                <span class="nav-text">Công việc đã làm</span>
            </a>
        </li>
    </ul>

    <hr>

    <!-- Người dùng -->
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser"
            data-bs-toggle="dropdown" aria-expanded="false">
            <i class="bi bi-person-circle fs-4 me-2"></i>
            <strong><?= htmlspecialchars($userName) ?></strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow">
            <li>
                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#profileModal">
                    <i class="bi bi-person me-2"></i> Hồ sơ cá nhân
                </a>
            </li>
            <li>
                <a href="#" class="dropdown-item" data-bs-toggle="modal" data-bs-target="#changePasswordModal">
                    <i class="bi bi-key me-2"></i> Đổi mật khẩu
                </a>
            </li>
            <li>
                <hr class="dropdown-divider">
            </li>
            <li>
                <a class="dropdown-item text-danger" href="../../Controllers/AuthController.php?action=logout">
                    <i class="bi bi-box-arrow-right me-2"></i> Đăng xuất
                </a>
            </li>
        </ul>

    </div>
</div>

<style>
    #sidebar .nav-link {
        border-radius: 8px;
        margin-bottom: 5px;
        transition: background 0.2s;
    }

    #sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
    }

    #sidebar.collapsed {
        width: 70px;
    }

    #sidebar.collapsed .nav-text {
        display: none;
    }

    #sidebar.collapsed .dropdown {
        display: none;
    }

    #sidebar.collapsed+#content {
        margin-left: 70px;
    }
</style>