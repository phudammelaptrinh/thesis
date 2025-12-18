<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Controllers/UserController.php';


$userCtrl = new UserController();
// Lấy danh sách người nhận việc (role: Nhận việc)
$receivers = $userCtrl->getRoleUsers('Nhận việc');

// Dự án hiện tại (nếu được truyền vào)
$currentProjectId = $_GET['project_id'] ?? null;
?>

<!-- Modal tạo công việc -->
<div class="modal fade" id="addTaskModal" tabindex="-1" aria-labelledby="addTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="addTaskForm" class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="addTaskModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Tạo công việc mới
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="project_id" value="<?= htmlspecialchars($currentProjectId) ?>">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên công việc</label>
                    <input type="text" name="title" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <input type="text" name="description" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mức độ ưu tiên</label>
                    <select name="priority" class="form-select" required>
                        <option value="">--Chọn độ ưu tiên--</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Hạn hoàn thành</label>
                    <input type="date" name="deadline" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Giao cho</label>
                    <select name="assignee_id" class="form-select" required>
                        <option value="">-- Chọn người phụ trách --</option>
                        <?php foreach ($receivers as $user): ?>
                            <option value="<?= htmlspecialchars($user['user_id']) ?>">
                                <?= htmlspecialchars($user['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById('addTaskForm');
        if (!form) return;

        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            // Tạo FormData và thêm action
            const formData = new FormData(form);
            formData.append('action', 'addTask');

            try {
                // Gửi request POST tới controller
                const url = (window.APP_BASE_URL || '') + '/apps/Controllers/TaskController.php';
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                // Kiểm tra kết quả
                if (result.success) {
                    // Đóng modal
                    const modal = bootstrap.Modal.getInstance(document.getElementById('addTaskModal'));
                    modal.hide();

                    // Thông báo thành công
                    alert(result.message);

                    // Reload nhẹ sau 0.5s
                    setTimeout(() => location.reload(), 500);
                } else {
                    // Hiển thị lỗi từ controller
                    alert(result.message);
                }
            } catch (err) {
                console.error('Lỗi khi gửi request:', err);
                alert('Đã xảy ra lỗi khi tạo công việc.');
            }
        });
    });
</script>