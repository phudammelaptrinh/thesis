<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

require_once __DIR__ . '/../../Controllers/TaskController.php';
require_once __DIR__ . '/../../Controllers/AuthController.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Nhận việc') {
    header("Location: " . url('apps/Views/auth/login.php'));
    exit;
}

$taskCtrl = new TaskController();
$tasks = $taskCtrl->indexTaskReceiver($_SESSION['user_id']);
?>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>

<style>
    .kanban-board {
        display: flex;
        gap: 15px;
        overflow-x: auto;
    }

    .kanban-column {
        background: #f8f9fa;
        border-radius: 10px;
        width: 25%;
        min-width: 280px;
        padding: 15px;
        height: 80vh;
        overflow-y: auto;
        box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
    }

    .kanban-title {
        font-weight: bold;
        font-size: 1.2rem;
        margin-bottom: 10px;
    }

    .task-card {
        background: white;
        border-radius: 10px;
        padding: 12px;
        margin-bottom: 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        cursor: grab;
    }

    .priority-low {
        background: #e7f5ff;
        color: #0c82c9;
    }

    .priority-medium {
        background: #fff4e6;
        color: #d97b00;
    }

    .priority-high {
        background: #ffe3e3;
        color: #d62828;
    }

    .priority-urgent {
        background: #ffccd5;
        color: #9d0208;
        font-weight: bold;
    }
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            📌 Bảng điều khiển công việc
        </h4>

        <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#reportModal">
            <i class="bi bi-upload"></i> Gửi báo cáo
        </button>
    </div>

    <div class="kanban-board">
        <?php
        $sections = [
            "pending" => ["label" => "⏳ Pending", "id" => "Pending"],
            "in_progress" => ["label" => "⚙️ In Progress", "id" => "In Progress"],
            "completed" => ["label" => "✅ Completed", "id" => "Completed"],
            "overdue" => ["label" => "⛔ Overdue", "id" => "Overdue"]
        ];

        foreach ($sections as $key => $sec):
            ?>
            <div class="kanban-column" id="<?= $sec['id'] ?>">
                <div class="kanban-title"><?= $sec['label'] ?></div>

                <?php foreach ($tasks[$key] as $task): ?>
                    <div class="task-card" data-id="<?= $task['task_id'] ?>"
                        data-title="<?= htmlspecialchars($task['title']) ?>"
                        data-createdby="<?= htmlspecialchars($task['full_name']) ?>"
                        data-project="<?= htmlspecialchars($task['project_name']) ?>" data-priority="<?= $task['priority'] ?>"
                        data-deadline="<?= $task['deadline'] ?>" data-desc="<?= htmlspecialchars($task['description']) ?>"
                        data-report="<?= htmlspecialchars($task['report_file'] ?? '') ?>"
                        data-report-original="<?= htmlspecialchars($task['report_file_original'] ?? '') ?>"
                        onclick="openTaskModal(this)">

                        <strong><?= htmlspecialchars($task['title']) ?></strong><br>
                        <small class="text-muted">
                            <?= $task['project_name'] ?>
                            &nbsp;|&nbsp;
                            <strong class="text-primary"><?= $task['progress'] ?>%</strong>
                        </small>

                        <div class="progress mt-1" style="height:6px;">
                            <div class="progress-bar bg-success" style="width: <?= $task['progress'] ?>%"></div>
                        </div>

                        <span class="badge 
                            <?= $task['priority'] == 'Low' ? 'priority-low' : '' ?>
                            <?= $task['priority'] == 'Medium' ? 'priority-medium' : '' ?>
                            <?= $task['priority'] == 'High' ? 'priority-high' : '' ?>
                            <?= $task['priority'] == 'Urgent' ? 'priority-urgent' : '' ?>
                        ">
                            <?= $task['priority'] ?>
                        </span>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endforeach ?>
    </div>
</div>

<!-- MODAL CHI TIẾT TASK -->
<div class="modal fade" id="taskModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p><strong>Dự án:</strong> <span id="taskProject"></span></p>
                <p><strong>Người giao:</strong> <span id="taskCreatedBy"></span></p>
                <p><strong>Ưu tiên:</strong> <span id="taskPriority"></span></p>
                <p><strong>Báo cáo:</strong> <span id="taskReport"></span></p>
                <p><strong>Deadline:</strong> <span id="taskDeadline" class="text-danger fw-bold"></span></p>
                <hr>
                <p><strong>Mô tả công việc:</strong></p>
                <div id="taskDesc" class="border rounded p-2 bg-light"></div>
            </div>
        </div>
    </div>
</div>

<?php include 'task_report.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // ===== GLOBAL FUNCTIONS =====
    window.openTaskModal = function (el) {
        const taskModalEl = document.getElementById('taskModal');
        if (!taskModalEl) return;

        document.getElementById('taskTitle').innerText = el.dataset.title;
        document.getElementById('taskProject').innerText = el.dataset.project;
        document.getElementById('taskCreatedBy').innerText = el.dataset.createdby;
        document.getElementById('taskPriority').innerText = el.dataset.priority;
        document.getElementById('taskDeadline').innerText = el.dataset.deadline;
        document.getElementById('taskDesc').innerText = el.dataset.desc;

        let report = el.dataset.report;
        let reportOriginal = el.dataset.reportOriginal;
        let taskId = el.dataset.id;
        let reportContainer = document.getElementById('taskReport');

        if (report && report !== "") {
            const reportUrl = (window.APP_BASE_URL || '') + `/Views/uploads/${report}`;
            const displayName = reportOriginal || report;
            reportContainer.innerHTML = `
                <a href="${reportUrl}" target="_blank" class="text-primary fw-semibold">${displayName}</a>
                <button type="button" class="btn btn-sm btn-danger ms-2" onclick="deleteReportFile('${taskId}', '${report}')">
                    <i class="bi bi-trash"></i> Xóa
                </button>
            `;
        } else {
            reportContainer.innerHTML = `<span class="text-muted">Chưa có báo cáo</span>`;
        }
        new bootstrap.Modal(taskModalEl).show();
    };

    window.deleteReportFile = async function (taskId, fileName) {
        if (!confirm('Bạn có chắc chắn muốn xóa file báo cáo này?')) return;

        const formData = new FormData();
        formData.append('action', 'deleteReportFile');
        formData.append('task_id', taskId);
        formData.append('file_name', fileName);

        try {
            const url = (window.APP_BASE_URL || '') + '/apps/Controllers/TaskController.php';
            const response = await fetch(url, { method: 'POST', body: formData });

            // Kiểm tra response
            const text = await response.text();
            console.log('Response text:', text);

            let result;
            try {
                result = JSON.parse(text);
            } catch (e) {
                console.error('JSON parse error:', e);
                console.error('Response was:', text);
                alert('Lỗi: Server trả về dữ liệu không hợp lệ.\n' + text.substring(0, 200));
                return;
            }

            if (result.success) {
                alert(result.message);

                // Cập nhật modal - xóa file khỏi giao diện
                const reportContainer = document.getElementById('taskReport');
                if (reportContainer) {
                    reportContainer.innerHTML = '<span class="text-muted">Chưa có báo cáo</span>';
                }

                // Cập nhật task card - xóa cả 2 attributes
                const taskCard = document.querySelector(`[data-id="${taskId}"]`);
                if (taskCard) {
                    taskCard.dataset.report = '';
                    taskCard.dataset.reportOriginal = '';
                }

                // Đóng modal
                const taskModalEl = document.getElementById('taskModal');
                if (taskModalEl) {
                    const modal = bootstrap.Modal.getInstance(taskModalEl);
                    if (modal) modal.hide();
                }

                // Reload để cập nhật từ database
                location.reload();
            } else {
                alert('Lỗi: ' + result.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Đã xảy ra lỗi khi xóa file: ' + error.message);
        }
    };

    // ===== DOM READY =====
    document.addEventListener("DOMContentLoaded", () => {
        const priorityOrder = { "Urgent": 1, "High": 2, "Medium": 3, "Low": 4 };

        function sortColumnTasks(columnId) {
            let column = document.getElementById(columnId);
            let tasks = Array.from(column.getElementsByClassName("task-card"));
            tasks.sort((a, b) => priorityOrder[a.dataset.priority] - priorityOrder[b.dataset.priority]);
            tasks.forEach(t => column.appendChild(t));
        }

        const columns = ["Pending", "In Progress", "Completed", "Overdue"];
        columns.forEach(colId => {
            sortColumnTasks(colId);
            new Sortable(document.getElementById(colId), {
                group: "shared",
                animation: 150,
                onAdd: async function (evt) {
                    let taskId = evt.item.dataset.id;
                    let newStatus = evt.to.id;
                    let formData = new FormData();
                    formData.append("task_id", taskId);
                    formData.append("status", newStatus);

                    const url = (window.APP_BASE_URL || '') + '/apps/Controllers/TaskController.php?action=changeStatus';
                    const res = await fetch(url, { method: "POST", body: formData });
                    const json = await res.json();

                    if (json.success) {
                        sortColumnTasks(newStatus);
                        const newProg = json.progress;
                        document.querySelectorAll(`.task-card[data-project="${evt.item.dataset.project}"]`)
                            .forEach(card => {
                                card.querySelector(".text-primary").innerText = newProg + "%";
                                card.querySelector(".progress-bar").style.width = newProg + "%";
                            });
                    }
                }
            });
        });
    });
</script>