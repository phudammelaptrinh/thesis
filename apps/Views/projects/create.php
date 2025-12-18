<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Controllers/UserController.php';

$userCtrl = new UserController();
// Lấy danh sách người nhận việc (role: Nhận việc)
$receivers = $userCtrl->getRoleUsers('Giao việc');
?>

<!-- 🧩 Modal thêm dự án -->
<div class="modal fade" id="addProjectModal" tabindex="-1" aria-labelledby="addProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addProjectModalLabel">
                    <i class="bi bi-kanban"></i> Thêm dự án mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Đóng"></button>
            </div>
            <form method="POST" action="../../Controllers/ProjectController.php">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="id" id="add_project_id">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên dự án</label>
                            <input type="text" name="project_name" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Mô tả</label>
                            <input type="text" name="description" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">--Chọn trạng thái--</option>
                                <option value="Planning">Planning</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Người nhận:</label>
                            <select name="assignee_name" class="form-select form-select-sm">
                                <option value="">--Chọn người nhận--</option>
                                <?php foreach ($receivers as $user): ?>
                                <option value="<?= htmlspecialchars($user['user_id']) ?>">
                                    <?= htmlspecialchars($user['full_name']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" name="action" value="add" class="btn btn-primary">
                        <i class="bi bi-save"></i> Thêm
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </form>
        </div>
    </div>
</div>