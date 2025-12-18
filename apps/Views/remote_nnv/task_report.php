<?php
// Giả sử user đã login
$userId = $_SESSION['user_id'] ?? null;

require_once __DIR__ . '/../../Controllers/TaskController.php';
$taskCtrl = new TaskController();

// Lấy các task Completed của user
$tasks = $taskCtrl->getTasksByUser($userId); // tạo hàm getTasksByUser nếu chưa có

// Lọc các task Completed
$completedTasks = array_filter($tasks, function ($t) {
    return $t['status'] === 'Completed';
});
?>


<!-- Modal gửi báo cáo -->
<div class="modal fade" id="reportModal" tabindex="-1" aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form id="reportForm" class="modal-content" enctype="multipart/form-data">

            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="reportModalLabel">
                    Gửi báo cáo
                    <span id="testModeIndicator" class="badge bg-warning text-dark ms-2" style="display:none;">DEV
                        MODE</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                <!-- Chọn task Completed -->
                <div class="mb-3">
                    <label class="form-label">Chọn Task báo cáo</label>
                    <select name="task_id" id="reportTaskId" class="form-select" required>
                        <option value="">-- Chọn Task --</option>
                        <?php foreach ($completedTasks as $task): ?>
                            <option value="<?= $task['task_id'] ?>"><?= htmlspecialchars($task['title']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nội dung báo cáo</label>
                    <textarea name="report_content" id="reportContent" class="form-control" rows="4"
                        required></textarea>
                </div>

                <div class="mb-3">
                    <label class="form-label">File đính kèm</label>
                    <input type="file" name="report_file" class="form-control" required>
                    <small class="text-muted" id="uploadLimitText">Max: 5MB</small>
                    <input type="hidden" name="test_mode" id="testModeValue" value="0">
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="submit" class="btn btn-success">Gửi</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reportForm = document.getElementById('reportForm');
        if (!reportForm) return;

        const testModeValue = document.getElementById('testModeValue');
        const testModeIndicator = document.getElementById('testModeIndicator');
        const uploadLimitText = document.getElementById('uploadLimitText');

        // Hàm cập nhật UI dựa trên global dev mode
        function updateDevModeUI() {
            const isDevMode = window.isDevMode && window.isDevMode();

            if (isDevMode) {
                testModeValue.value = '1';
                testModeIndicator.style.display = 'inline-block';
                uploadLimitText.textContent = 'Max: 10MB (Dev Mode - No Security Scan)';
                uploadLimitText.classList.add('text-warning');
            } else {
                testModeValue.value = '0';
                testModeIndicator.style.display = 'none';
                uploadLimitText.textContent = 'Max: 5MB (Production Mode)';
                uploadLimitText.classList.remove('text-warning');
            }
        }

        function updateCompletedTasksInModal() {
            const select = document.getElementById('reportTaskId');
            if (!select) return;

            // Xóa option cũ
            select.querySelectorAll('option:not([value=""])').forEach(opt => opt.remove());

            // Lấy tất cả task trong cột Completed
            const completedTasks = document.querySelectorAll('#Completed .task-card');

            completedTasks.forEach(taskEl => {
                const option = document.createElement('option');
                option.value = taskEl.dataset.id;
                option.textContent = taskEl.dataset.title;
                select.appendChild(option);
            });
        }

        // Cập nhật UI khi mở modal
        const reportModalEl = document.getElementById('reportModal');
        if (reportModalEl) {
            reportModalEl.addEventListener('show.bs.modal', function () {
                updateDevModeUI();
                updateCompletedTasksInModal();
            });
        }

        reportForm.addEventListener('submit', async function (e) {
            e.preventDefault();

            const formData = new FormData(this);
            formData.append('action', 'sendReport');

            try {
                const url = (window.APP_BASE_URL || '') + '/apps/Controllers/TaskController.php';
                const res = await fetch(url, {
                    method: 'POST',
                    body: formData
                });
                const json = await res.json();

                if (json.success) {
                    alert(json.message);
                    bootstrap.Modal.getInstance(document.getElementById('reportModal')).hide();
                    location.reload();
                } else {
                    alert(json.message);
                }
            } catch (err) {
                console.error(err);
                alert('Đã xảy ra lỗi khi gửi báo cáo.');
            }
        });

        // Trong SortableJS, khi kéo task vào Completed
        const completedCol = document.getElementById('Completed');
        if (completedCol && typeof Sortable !== 'undefined') {
            new Sortable(completedCol, {
                group: 'shared',
                animation: 150,
                onAdd: function (evt) {
                    const taskId = evt.item.dataset.id;
                    // Cập nhật DB nếu cần...
                    updateCompletedTasksInModal(); // ⭐ thêm task mới vào modal ngay lập tức
                }
            });
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>