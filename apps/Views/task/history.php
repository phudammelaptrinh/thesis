<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Controllers/TaskController.php';
require_once __DIR__ . '/../../Controllers/UserController.php';

$taskCtrl = new TaskController();
$userCtrl = new UserController();

// GET FILTER: chọn người nhận việc
$receiverId = $_GET['receiver'] ?? null;

// Lấy danh sách người nhận việc (role "Nhận việc")
$receivers = $userCtrl->getUserRole_ReceiverTasks();

// Lấy thông tin người được chọn
$receiverInfo = null;
if ($receiverId) {
    $receiverInfo = $userCtrl->getUserById($receiverId);
}

// Lấy tất cả task của người nhận được chọn
$tasksByProject = [];
if ($receiverId) {
    $tasks = $taskCtrl->getTasks_Project($receiverId); //

    // Nhóm task theo dự án
    foreach ($tasks as $task) {
        $projectName = $task['project_name'] ?? 'Không rõ dự án';
        if (!isset($tasksByProject[$projectName])) {
            $tasksByProject[$projectName] = [];
        }
        $tasksByProject[$projectName][] = $task;
    }
}

// --- TÍNH TỔNG QUAN CÔNG VIỆC ---
$totalProjects = 0;
$totalTasks = 0;
$pending = $inProgress = $completed = $overdue = 0;

if ($receiverId && !empty($tasks)) {
    $totalProjects = count($tasksByProject);
    $totalTasks = count($tasks);
    // Đếm theo trạng thái
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
}
?>

<div class="container mt-4">
    <!-- Filter chọn người nhận việc -->
    <form method="GET" class="row g-3 mb-4 align-items-end">
        <input type="hidden" name="page" value="history">

        <div class="col-md-4">
            <label class="form-label fw-bold">Chọn người nhận việc:</label>
            <select class="form-select" name="receiver">
                <option value="">-- Người nhận việc --</option>
                <?php foreach ($receivers as $u): ?>
                    <option value="<?= $u['user_id'] ?>" <?= ($receiverId == $u['user_id'] ? 'selected' : '') ?>>
                        <?= htmlspecialchars($u['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2 d-flex">
            <button class="btn btn-primary w-100">Lọc dữ liệu</button>
        </div>
    </form>

    <?php if (!$receiverId): ?>
        <div class="alert alert-warning text-center">Vui lòng chọn một người nhận việc để hiển thị task.</div>
    <?php elseif (empty($tasksByProject)): ?>
        <div class="alert alert-info text-center">Người nhận này chưa nhận task nào.</div>
    <?php else: ?>
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
        <h5 class="bi bi-clock-history">Lịch sử công việc của:
            <?= htmlspecialchars($receiverInfo['full_name'] ?? '') ?>
        </h5>
        <?php foreach ($tasksByProject as $projectName => $projectTasks): ?>
            <?php $progress = $projectTasks[0]['progress'] ?? 0; ?>
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center mt-4">
                    <h5 class="m-0">Dự án: <?= htmlspecialchars($projectName) ?></h5>
                    <span class="badge bg-primary">Tiến độ: <?= $progress ?>%</span>
                </div>
                <table class="table table-bordered align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Tên task</th>
                            <th>Mô tả</th>
                            <th>Trạng thái</th>
                            <th>Deadline</th>
                            <th>Báo cáo</th>
                            <th>Nội dung báo cáo</th>
                            <th>Kết quả</th>
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
                                <td><?= htmlspecialchars($task['deadline'] ?? '') ?></td>
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

        async function openViewTaskModal(taskId) {
            if (!viewTaskModal) return;

            try {
                const res = await fetch(`${window.APP_BASE_URL || ''}/apps/Controllers/TaskController.php?action=getTasks_Id&task_id=${taskId}`);
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
        }

        // Gán sự kiện cho tất cả nút Xem
        document.querySelectorAll('.btn-view-task').forEach(btn => {
            btn.addEventListener('click', () => openViewTaskModal(btn.dataset.taskId));
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>