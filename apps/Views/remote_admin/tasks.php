<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Controllers/TaskController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../Controllers/UserController.php';

$taskCtrl = new TaskController();
$projectCtrl = new ProjectController();
$userCtrl = new UserController();

$userId = $_SESSION['user_id'] ?? null;
// ✅ Lấy thông tin người dùng đăng nhập từ session
if (isset($_SESSION['user_id'])) {
    $currentUser = $userCtrl->getUserById($_SESSION['user_id']);
}

// Lấy role
$role = $_SESSION['role'] ?? null;

// Lấy danh sách dự án
// Admin xem tất cả projects, NGV chỉ xem projects được assign
if ($role === 'Admin') {
    $projects = $projectCtrl->index(); // Lấy tất cả projects
} else {
    $projects = $projectCtrl->getProjectsByUser($userId); // Lấy theo user
}

// Dự án hiện tại
$currentProjectId = $_GET['project_id'] ?? ($projects[0]['project_id'] ?? null);
$currentProject = $currentProjectId ? $projectCtrl->getProject($currentProjectId) : null;
$tasks = $currentProjectId ? $taskCtrl->getTasksByProjectId($currentProjectId) : [];

// Danh sách người nhận việc
$receivers = $userCtrl->getRoleUsers('Nhận việc');
?>

<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-dark mb-0">
            <i class="bi bi-list-task me-2 text-primary"></i>Danh sách công việc
        </h4>
        <?php if ($currentProject): ?>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addTaskModal">
                <i class="bi bi-plus-circle"></i> Tạo công việc mới
            </button>
        <?php endif; ?>
    </div>

    <!-- Chọn dự án -->
    <div class="mb-4">
        <label class="fw-semibold">Chọn dự án:</label>
        <select class="form-select w-auto d-inline-block" onchange="changeProject(this)">
            <?php foreach ($projects as $p): ?>
                <option value="<?= $p['project_id'] ?>" <?= ($p['project_id'] == $currentProjectId) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['project_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <?php if ($currentProject): ?>
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white fw-semibold">
                Dự án: <?= htmlspecialchars($currentProject['project_name']) ?>
            </div>
            <div class="card-body">
                <?php if (empty($tasks)): ?>
                    <p class="text-muted text-center">Chưa có công việc nào trong dự án này.</p>
                <?php else: ?>
                    <table class="table table-striped align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Tên công việc</th>
                                <th>Mô tả</th>
                                <th>Người phụ trách</th>
                                <th>Hạn hoàn thành</th>
                                <th>Độ ưu tiên</th>
                                <th>Trạng thái</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?= htmlspecialchars($task['title']) ?></td>
                                    <td><?= htmlspecialchars($task['description']) ?></td>
                                    <td><?= htmlspecialchars($task['assignee_name'] ?? '—') ?></td>
                                    <td><?= htmlspecialchars($task['deadline']) ?></td>
                                    <td><?= htmlspecialchars($task['priority']) ?></td>
                                    <td>
                                        <?php
                                        $status = $task['status'];
                                        $badge = [
                                            'Pending' => 'warning',
                                            'In Progress' => 'info',
                                            'Completed' => 'success'
                                        ][$status] ?? 'secondary';
                                        ?>
                                        <span class="badge bg-<?= $badge ?>"><?= $status ?></span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary"
                                            onclick="openEditTaskModal(<?= $task['task_id'] ?>)">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteTask(<?= $task['task_id'] ?>)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Include modals -->
        <?php include '../task/create.php'; ?>
        <?php include '../task/edit.php'; ?>
    <?php endif; ?>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const editModalEl = document.getElementById('editTaskModal');
        const editModal = editModalEl ? new bootstrap.Modal(editModalEl) : null;

        window.changeProject = function (select) {
            const url = (window.APP_BASE_URL || '') + `/apps/Views/admin/index.php?page=tasks&project_id=${select.value}`;
            window.location.href = url;
        };

        window.deleteTask = function (taskId) {
            if (!confirm("Bạn có chắc muốn xóa công việc này không?")) return;
            const url = (window.APP_BASE_URL || '') + `/apps/Controllers/TaskController.php?action=delete&task_id=${taskId}`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    alert(data.message);
                    if (data.success) location.reload();
                });
        };

        window.openEditTaskModal = function (taskId) {
            if (!editModal) return;

            const url = (window.APP_BASE_URL || '') + `/apps/Controllers/TaskController.php?action=getTaskById&task_id=${taskId}`;
            fetch(url)
                .then(res => res.json())
                .then(data => {
                    if (!data.success) return alert(data.message);

                    const task = data.data;
                    document.getElementById('editTaskId').value = task.task_id;
                    document.getElementById('editProjectId').value = task.project_id;
                    document.getElementById('editTitle').value = task.title;
                    document.getElementById('editDescription').value = task.description;
                    document.getElementById('editDeadline').value = task.deadline;
                    document.getElementById('editAssignee').value = task.assigned_to;
                    document.getElementById('editStatus').value = task.status;

                    editModal.show();
                })
                .catch(err => console.error(err));
        };
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>