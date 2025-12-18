<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Controllers/UserController.php';

$userCtrl = new UserController();
// Lấy danh sách người nhận việc (role: Nhận việc)
$receivers = $userCtrl->getRoleUsers('Giao việc');
?>

<!-- 🧩 Modal sửa dự án -->
<div class="modal fade" id="editProjectModal" tabindex="-1" aria-labelledby="editProjectModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title" id="editProjectModalLabel">
                    <i class="bi bi-pencil-square"></i> Cập nhật thông tin dự án
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
            </div>

            <form id="editProjectForm" method="POST" action="../../Controllers/ProjectController.php">
                <input type="hidden" name="action" value="update">

                <div class="modal-body">
                    <input type="hidden" name="project_id" id="edit_project_id">
                    <input type="hidden" name="created_by" value="1"> <!-- Người tạo mặc định là Admin -->

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Tên dự án</label>
                            <input type="text" name="project_name" id="edit_project_name"
                                class="form-control form-control-sm" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Người nhận</label>
                            <select name="assignee_id" id="edit_Assignee" class="form-select form-select-sm">
                                <option value="">-- Chọn người phụ trách --</option>
                                <?php foreach ($receivers as $user): ?>
                                    <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['full_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Trạng thái</label>
                            <select name="status" id="edit_status" class="form-select form-select-sm" required>
                                <option value="">--Chọn trạng thái--</option>
                                <option value="Planning">Planning</option>
                                <option value="Completed">Completed</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" name="start_date" id="edit_start_date"
                                class="form-control form-control-sm" required>
                        </div>


                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="date" name="end_date" id="edit_end_date" class="form-control form-control-sm"
                                required>
                        </div>

                        <div class="col-md-12">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" id="edit_description" rows="3"
                                class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu thay đổi
                    </button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                </div>
            </form>
        </div>
    </div>
</div>