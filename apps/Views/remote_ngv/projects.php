<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Controllers/UserController.php';
$userCtrl = new UserController();
require_once __DIR__ . '/../../Controllers/ProjectController.php';
$controller = new ProjectController();
$projects = $controller->index();
$currentUser = null;
// $assignedUserId = $projects['assigned_to'] ?? null;
// $isAssigned = $assignedUserId == $_SESSION['user_id'];

// ✅ Lấy thông tin người dùng đăng nhập từ session
if (isset($_SESSION['user_id'])) {
    $currentUser = $userCtrl->getUserById($_SESSION['user_id']);
}

$userName = $currentUser['full_name'] ?? 'Người giao việc';
?>

<!-- Danh sách dự án -->
<div class="card shadow-sm border-0">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="m-0">📁 Danh sách dự án đang quản lý</h5>
        <small class="text-muted">Người giao việc: <?= htmlspecialchars($userName) ?></small>
    </div>

    <div class="card-body">
        <table class="table table-hover align-middle">
            <thead class="table-secondary">
                <tr>
                    <th>#</th>
                    <th>Tên dự án</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Tiến độ (%)</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $i => $project): ?>
                        <?php
                        $isAssigned = ($project['assigned_to'] ?? null) == ($_SESSION['user_id'] ?? null);
                        ?>
                        <tr class="<?= $isAssigned ? 'table-success' : '' ?>">
                            <td><?= $i + 1 ?></td>
                            <td><?= htmlspecialchars($project['project_name']) ?></td>
                            <td><?= htmlspecialchars($project['start_date']) ?></td>
                            <td><?= htmlspecialchars($project['end_date']) ?></td>
                            <td><?= (int) $project['progress'] ?>%</td>
                            <td>
                                <?php
                                $statusClass = match (strtolower($project['status'])) {
                                    'completed' => 'success',
                                    'planning' => 'info',
                                    default => 'secondary'
                                };
                                ?>
                                <span class="badge bg-<?= $statusClass ?>">
                                    <?= htmlspecialchars($project['status']) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($isAssigned): ?>
                                    <span class="badge bg-warning me-2">
                                        <i class="bi bi-check-circle"></i> Đã nhận
                                    </span>
                                    <!-- NÚT HỦY NHẬN -->
                                    <button class="btn btn-danger btn-sm btn-unassign" data-id="<?= $project['project_id'] ?>">
                                        <i class="bi bi-x-circle"></i>
                                    </button>
                                <?php else: ?>
                                    <!-- NÚT NHẬN -->
                                    <button class="btn btn-primary btn-sm btn-assign" data-id="<?= $project['project_id'] ?>">
                                        <i class="bi bi-person-plus"></i> Nhận dự án
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Chưa có dự án nào được giao quản lý.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Nhận dự án
        document.querySelectorAll('.btn-assign').forEach(btn => {
            btn.addEventListener('click', function () {
                const projectId = this.dataset.id;
                if (!projectId) return;
                // Thêm confirm trước khi nhận dự án
                const isConfirm = confirm("Bạn có chắc muốn nhận dự án này không?");
                if (!isConfirm) return;
                // Hiển thị trạng thái loading
                const originalText = this.innerHTML;
                this.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';
                this.disabled = true;

                // Gửi yêu cầu nhận dự án
                const url = (window.APP_BASE_URL || '') + '/apps/Controllers/ProjectController.php?action=assign';
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'project_id=' + encodeURIComponent(projectId)
                })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);

                        if (data.success) {
                            location.reload();
                        } else {
                            // ❌ Nếu thất bại (ví dụ 100%), khôi phục lại nút
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error('❌ Lỗi:', err);
                        alert('Không thể kết nối máy chủ!');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    });
            });
        });
        //  NÚT HỦY NHẬN DỰ ÁN
        // ==========================
        document.querySelectorAll('.btn-unassign').forEach(btn => {
            btn.addEventListener('click', function () {

                if (!confirm("Bạn có chắc muốn hủy nhận dự án này?")) return;

                const projectId = this.dataset.id;

                const originalText = this.innerHTML;
                this.innerHTML =
                    '<span class="spinner-border spinner-border-sm"></span> Đang xử lý...';
                this.disabled = true;

                const url = (window.APP_BASE_URL || '') + '/apps/Controllers/ProjectController.php?action=unassign';
                fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: 'project_id=' + encodeURIComponent(projectId)
                })
                    .then(res => res.json())
                    .then(data => {
                        alert(data.message);

                        if (data.success) {
                            location.reload();
                        } else {
                            this.innerHTML = originalText;
                            this.disabled = false;
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Không thể kết nối máy chủ.');
                        this.innerHTML = originalText;
                        this.disabled = false;
                    });
            });
        });
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


<style>
    .table-info {
        background-color: #cfe2ff !important;
        transition: background-color 0.3s;
    }

    .table-success {
        background-color: #33a6c9ff !important;
    }
</style>