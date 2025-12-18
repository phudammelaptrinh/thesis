<!-- Modal chỉnh sửa công việc -->
<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editTaskForm" class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="editTaskModalLabel"><i class="bi bi-pencil-square me-2"></i>Chỉnh sửa công
                    việc</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" name="task_id" id="editTaskId">
                <input type="hidden" name="project_id" id="editProjectId">

                <div class="mb-3">
                    <label class="form-label fw-semibold">Tên công việc</label>
                    <input type="text" name="title" id="editTitle" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mô tả</label>
                    <input type="text" name="description" id="editDescription" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Mức độ ưu tiên</label>
                    <select name="priority" id="editPriority" class="form-select" required>
                        <option value="">--Chọn độ ưu tiên--</option>
                        <option value="Low">Low</option>
                        <option value="Medium">Medium</option>
                        <option value="High">High</option>
                        <option value="Urgent">Urgent</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Hạn hoàn thành</label>
                    <input type="date" name="deadline" id="editDeadline" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Giao cho</label>
                    <select name="assignee_id" id="editAssignee" class="form-select" required>
                        <option value="">-- Chọn người phụ trách --</option>
                        <?php foreach ($receivers as $user): ?>
                            <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['full_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Trạng thái</label>
                    <select name="status" id="editStatus" class="form-select" required>
                        <option value="Pending">Chưa bắt đầu</option>
                        <option value="In Progress">Đang tiến hành</option>
                        <option value="Completed">Hoàn thành</option>
                        <option value="Overdue">Trễ hạn</option>
                    </select>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-primary">Lưu</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('editTaskForm')?.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(e.target);
        formData.append('action', 'updateTask');

        try {
            const url = (window.APP_BASE_URL || '') + '/apps/Controllers/TaskController.php';
            const res = await fetch(url, {
                method: 'POST',
                body: formData
            });

            const result = await res.json();

            if (result.success) {
                // Hiển thị thông báo thành công
                alert(result.message);

                // Đóng modal
                const modalEl = document.getElementById('editTaskModal');
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();

                // Reload ngay sau khi người dùng bấm OK
                location.reload();
            } else {
                alert(result.message);
            }

        } catch (err) {
            console.error('Lỗi khi gửi yêu cầu:', err);
            alert('Đã xảy ra lỗi khi cập nhật công việc.');
        }
    });
</script>