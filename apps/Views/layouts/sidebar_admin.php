<div id="sidebar" class="d-flex flex-column flex-shrink-0 p-3 bg-dark text-white">
    <div class="d-flex align-items-center justify-content-between mb-3">
        <span class="fs-4 fw-bold"><i class="bi bi-grid"></i> TASKBB-ADMIN</span>
    </div>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="index.php?page=generals" class="nav-link text-white"><i class="bi bi-view-list"></i> <span
                    class="nav-text">Tổng
                    quan</span></a></li>
        <li><a href="index.php?page=users" class="nav-link text-white"><i class="bi bi-people"></i> <span
                    class="nav-text">Người dùng</span></a></li>
        <li><a href="index.php?page=projects" class="nav-link text-white"><i class="bi bi-folder2"></i> <span
                    class="nav-text">Dự án</span></a></li>
        <li><a href="index.php?page=reports" class="nav-link text-white"><i class="bi bi-bar-chart"></i> <span
                    class="nav-text">Thống kê người dùng</span></a></li>
    </ul>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="index.php?page=projects_assigned" class="nav-link text-white"><i class="bi bi-people"></i> <span
                    class="nav-text">Nhận dự án</span></a></li>
        <li><a href="index.php?page=tasks" class="nav-link text-white"><i class="bi bi-folder2"></i> <span
                    class="nav-text">Giao công việc</span></a></li>
        <li><a href="index.php?page=tasks_review" class="nav-link text-white"><i class="bi bi-card-checklist"></i>
                <span class="nav-text">Đánh giá công việc</span></a></li>
        <li><a href="index.php?page=reports_ngv" class="nav-link text-white"><i class="bi bi-bar-chart"></i> <span
                    class="nav-text">Thống kê dự án</span></a></li>
    </ul>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li><a href="index.php?page=tasks_assigned" class="nav-link text-white"><i class="bi bi-people"></i> <span
                    class="nav-text">Theo dõi công việc</span></a></li>
        <li><a href="index.php?page=history" class="nav-link text-white"><i class="bi bi-clock-history"></i> <span
                    class="nav-text">Công việc đã làm</span></a></li>
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