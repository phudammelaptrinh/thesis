<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Controllers/TaskController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../Controllers/UserController.php';

$userId = $_SESSION['user_id'];



$taskCtrl = new TaskController();
$projectCtrl = new ProjectController();
$userCtrl = new UserController();

// Lấy tên user
$user = $userCtrl->getUserById($userId);
$username = $user['full_name'] ?? 'Unknown';

// Lấy tất cả task mà user này nhận
$tasks = $taskCtrl->getTasks_Project($userId);

// Nhóm task theo dự án
$tasksByProject = [];
foreach ($tasks as $task) {
    $projectName = $task['project_name'] ?? 'Không rõ dự án';
    if (!isset($tasksByProject[$projectName])) {
        $tasksByProject[$projectName] = [];
    }
    $tasksByProject[$projectName][] = $task;
}

// --- TÍNH TỔNG QUAN CÔNG VIỆC ---
$totalProjects = count($tasksByProject);
$totalTasks = count($tasks);
// Đếm theo trạng thái
$pending = 0;
$inProgress = 0;
$completed = 0;
$overdue = 0;
foreach ($tasks as $t) {
    switch ($t['status']) {
        case 'Pending':
            $pending++;
            break;
        case 'In Progress':
            $inProgress++;
            break;
        case 'Completed':
            $completed++;
            break;
        case 'Overdue':
            $overdue++;
            break;
    }
}
?>

<div class="container mt-4">
    <!-- TỔNG QUAN CÔNG VIỆC -->
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="mb-3">Tổng quan công việc</h4>

            <div class="row text-center">

                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-primary mb-1"><?= $totalProjects ?></h5>
                        <small class="text-muted">Dự án tham gia</small>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-dark mb-1"><?= $totalTasks ?></h5>
                        <small class="text-muted">Task được giao</small>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-secondary mb-1"><?= $pending ?></h5>
                        <small class="text-muted">Task chưa làm</small>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-info mb-1"><?= $inProgress ?></h5>
                        <small class="text-muted">Task đang làm</small>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-success mb-1"><?= $completed ?></h5>
                        <small class="text-muted">Task hoàn thành</small>
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="p-3 border rounded bg-light">
                        <h5 class="text-danger mb-1"><?= $overdue ?></h5>
                        <small class="text-muted">Task trễ hạn</small>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <h3 class="bi bi-clock-history me-2"> Lịch sử công việc của <?= htmlspecialchars($username) ?></h3>

    <?php if (empty($tasksByProject)): ?>
        <div class="alert alert-info mt-3">Bạn chưa nhận task nào.</div>
    <?php else: ?>
        <?php foreach ($tasksByProject as $projectName => $projectTasks): ?>
            <?php $progress = $projectTasks[0]['progress'] ?? 0; ?>
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <h5 class="m-0">Dự án: <?= htmlspecialchars($projectName) ?></h5>
                    <span class="badge bg-primary">Tiến độ: <?= $progress ?>%</span>
                </div>
                <table class="table table-bordered mt-2 align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Tên task</th>
                            <th>Mô tả</th>
                            <th>Trạng thái</th>
                            <th>Báo cáo</th>
                            <th>Nội dung báo cáo</th>
                            <th>Kết quả</th>
                            <th>Ngày nhận</th>
                            <th>Ngày cập nhật</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projectTasks as $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td><?= htmlspecialchars($task['description']) ?></td>
                                <td>
                                    <?php
                                    $status = $task['status'];
                                    $badge = [
                                        'Pending' => 'secondary',
                                        'In Progress' => 'info',
                                        'Completed' => 'success',
                                        'Overdue' => 'warning'
                                    ][$status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= $status ?></span>
                                </td>
                                <td>
                                    <?php if (!empty($task['report_file'])): ?>
                                        <a href="<?= VIEWS_UPLOAD_URL ?>/<?= urlencode($task['report_file']) ?>" target="_blank">
                                            <?= htmlspecialchars($task['report_file']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa có file</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= htmlspecialchars($task['report_content'] ?? '') ?></td>
                                <td>
                                    <?php
                                    $result = $task['result'] ?? null;
                                    $badgeColors = [
                                        'Xuất sắc' => 'success',
                                        'Tốt' => 'primary',
                                        'Khá' => 'warning',
                                        'Trung bình' => 'secondary'
                                    ];
                                    if ($result) {
                                        $color = $badgeColors[$result] ?? 'dark';
                                        echo "<span class='badge bg-$color'>" . htmlspecialchars($result) . "</span>";
                                    } else {
                                        echo "<span class='badge bg-secondary'>Chưa đánh giá</span>";
                                    }
                                    ?>
                                </td>
                                <td><?= htmlspecialchars($task['created_at'] ?? '') ?></td>
                                <td><?= htmlspecialchars($task['updated_at'] ?? '') ?></td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-primary btn-view-task"
                                        data-task-id="<?= $task['task_id'] ?>">
                                        Xem
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include '../task/detail.php' ?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const viewTaskModalEl = document.getElementById('viewTaskModal');
        const viewTaskModal = viewTaskModalEl ? new bootstrap.Modal(viewTaskModalEl) : null;

        window.openViewTaskModal = async function (taskId) {
            if (!viewTaskModal) return;

            try {
                const url = (window.APP_BASE_URL || '') + `/apps/Controllers/TaskController.php?action=getTasks_Id&task_id=${taskId}`;
                const res = await fetch(url);
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Lỗi tải task');

                const task = data.data;
                document.getElementById('viewTaskId').value = task.task_id;
                document.getElementById('viewTitle').innerText = task.title;
                document.getElementById('viewDescription').innerText = task.description;
                document.getElementById('viewAssignee').innerText = task.assignee_name;
                document.getElementById('viewStatus').innerText = task.status;
                document.getElementById('viewDeadline').innerText = task.deadline;
                document.getElementById('viewReportContent').innerText = task.report_content || 'Chưa có nội dung';
                if (task.report_file) {
                    const fileUrl = (window.APP_BASE_URL || '') + `/Views/uploads/${encodeURIComponent(task.report_file)}`;
                    document.getElementById('viewReportFile').innerHTML =
                        `<a href="${fileUrl}" target="_blank">${task.report_file}</a>`;
                } else {
                    document.getElementById('viewReportFile').innerText = 'Chưa có file';
                }
                document.getElementById('viewResult').innerText = task.result || 'Chưa đánh giá';
                viewTaskModal.show();
            } catch (err) {
                console.error(err);
                alert('Không thể tải dữ liệu task.');
            }
        };

        // Gán sự kiện cho tất cả nút Xem
        document.querySelectorAll('.btn-view-task').forEach(btn => {
            btn.addEventListener('click', () => openViewTaskModal(btn.dataset.taskId));
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>