<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Controllers/TaskController.php';
require_once __DIR__ . '/../../Controllers/UserController.php';

if (!isset($_SESSION['user_id'])) {
    die("<div class='alert alert-warning'>Bạn chưa đăng nhập. Vui lòng login trước khi xem trang này.</div>");
}

$userId = $_SESSION['user_id'];

$taskCtrl = new TaskController();
$userCtrl = new UserController();

// Lấy tất cả task Completed của user
$tasks = $taskCtrl->getCompletedTasksByUser($userId);

// Nhóm task theo project
$tasksByProject = [];
foreach ($tasks as $task) {
    $projectName = $task['project_name'];
    if (!isset($tasksByProject[$projectName])) {
        $tasksByProject[$projectName] = [];
    }
    $tasksByProject[$projectName][] = $task;
}
?>

<div class="container mt-4">
    <h3 class="bi bi-card-checklist me-2"> Đánh giá kết quả công việc</h3>

    <?php if (empty($tasksByProject)): ?>
        <div class="alert alert-info mt-3">Không có task nào đã hoàn thành hoặc chưa có đánh giá.</div>
    <?php else: ?>
        <?php foreach ($tasksByProject as $projectName => $projectTasks): ?>
            <div class="mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <span>
                        <strong>
                            <h5>Dự án: <?= htmlspecialchars($projectName) ?></h5>
                        </strong>
                    </span>
                    <span class="badge bg-primary">Tiến độ: <?= $projectTasks[0]['progress'] ?? 0 ?>%</span>
                </div>
                <table class="table table-bordered mt-2 align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th>Tên task</th>
                            <th>Kết quả</th>
                            <th>Đánh giá</th>
                            <th class="text-center">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($projectTasks as $task): ?>
                            <tr>
                                <td><?= htmlspecialchars($task['title']) ?></td>
                                <td>
                                    <?php if (!empty($task['report_file'])): ?>
                                        <a href="<?= VIEWS_UPLOAD_URL ?>/<?= urlencode($task['report_file']) ?>" target="_blank">
                                            <?= htmlspecialchars($task['report_file']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">Chưa có file</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $result = $task['result'] ?? null;

                                    // Gán màu theo mức độ
                                    $badgeColors = [
                                        'Xuất sắc' => 'success',   // xanh lá
                                        'Tốt' => 'primary',   // xanh dương
                                        'Khá' => 'warning',   // vàng
                                        'Trung bình' => 'secondary'  // xám
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
                                    <button class="btn btn-sm btn-outline-primary btn-review"
                                        data-task-id="<?= $task['task_id'] ?>">
                                        <?= !empty($task['result']) ? "Cập nhật" : "Đánh giá" ?>
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

<?php include '../task/review_detail.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const reviewModalEl = document.getElementById('reviewTaskModal');
        const reviewModal = reviewModalEl ? new bootstrap.Modal(reviewModalEl) : null;

        // Function mở modal đánh giá task
        window.openReviewModal = async function (taskId) {
            if (!reviewModal) return;

            try {
                const res = await fetch(`${window.APP_BASE_URL || ''}/apps/Controllers/TaskController.php?action=getTasks_Id&task_id=${taskId}`);
                const data = await res.json();
                if (!data.success) throw new Error(data.message || 'Lỗi tải task');

                const task = data.data;
                document.getElementById('reviewTaskId').value = task.task_id;
                document.getElementById('reviewTitle').value = task.title;
                document.getElementById('reviewDescription').value = task.description;
                document.getElementById('reviewAssignee').value = task.assignee_name;
                document.getElementById('reviewStatus').value = task.status;
                document.getElementById('reviewDeadline').value = task.deadline;
                document.getElementById('reviewReportContent').value = task.report_content;
                document.getElementById('reviewResult').value = task.result || '';

                reviewModal.show();
            } catch (err) {
                console.error(err);
                alert('Không thể tải dữ liệu task.');
            }
        };

        // Gán cho tất cả nút đánh giá
        document.querySelectorAll('.btn-review').forEach(btn => {
            btn.addEventListener('click', () => openReviewModal(btn.dataset.taskId));
        });
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>