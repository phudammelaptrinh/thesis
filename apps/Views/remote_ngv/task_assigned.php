<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();

require_once __DIR__ . '/../../Controllers/TaskController.php';
require_once __DIR__ . '/../../Controllers/UserController.php';

$userCtrl = new UserController();
// Lấy danh sách người nhận việc (role: Nhận việc)
$receivers = $userCtrl->getUserRole_ReceiverTasks();

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

        <div class="d-flex align-items-center gap-2">
            <label class="fw-bold">Người nhận việc:</label>

            <select id="userSelect" class="form-select form-select-sm" style="width: 220px;">
                <option value="">-- Chọn người nhận việc --</option>
                <?php
                foreach ($receivers as $u):
                    ?>
                    <option value="<?= $u['assigned_to'] ?>">
                        <?= htmlspecialchars($u['full_name']) ?>
                    </option>
                <?php endforeach ?>
            </select>
        </div>
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
                        data-report="<?= htmlspecialchars($task['report_file'] ?? 'Chưa có báo cáo') ?>"
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

<?php include '../remote_nnv/task_report.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const priorityOrder = {
            "Urgent": 1,
            "High": 2,
            "Medium": 3,
            "Low": 4
        };

        function sortColumnTasks(columnId) {
            let column = document.getElementById(columnId);
            let tasks = Array.from(column.getElementsByClassName("task-card"));

            tasks.sort((a, b) => {
                let p1 = priorityOrder[a.dataset.priority];
                let p2 = priorityOrder[b.dataset.priority];
                return p1 - p2; // số nhỏ hơn đứng trên (Urgent = 1)
            });

            tasks.forEach(t => column.appendChild(t));
        }

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
            let reportContainer = document.getElementById('taskReport');

            if (report && report !== "") {
                const reportUrl = (window.APP_BASE_URL || '') + `/Views/uploads/${report}`;
                reportContainer.innerHTML = `
            <a href="${reportUrl}" target="_blank" class="text-primary fw-semibold">
                ${report}
            </a>
        `;
            } else {
                reportContainer.innerHTML = `<span class="text-muted">Chưa có báo cáo</span>`;
            }
            new bootstrap.Modal(taskModalEl).show();
        };

        window.openReportModal = function (taskId) {
            const url = (window.APP_BASE_URL || '') + '/apps/Views/remote_nnv/task_report.php?task_id=' + taskId;
            fetch(url)
                .then(res => res.text())
                .then(html => {
                    // Bơm HTML modal vào trang
                    document.getElementById("reportModalContainer").innerHTML = html;

                    // Gán task_id vào input hidden
                    const input = document.getElementById("reportTaskId");
                    if (input) input.value = taskId;

                    // Mở modal
                    const reportModalEl = document.getElementById("reportModal");
                    if (reportModalEl) {
                        let modal = new bootstrap.Modal(reportModalEl);
                        modal.show();
                    }
                })
                .catch(err => console.error("Lỗi load modal:", err));
        };

        // Kéo - Thả (SortableJS)
        const columns = ["Pending", "In Progress", "Completed", "Overdue"];

        columns.forEach(colId => {
            sortColumnTasks(colId); // sắp xếp khi load trang
        });

        columns.forEach(colId => {
            new Sortable(document.getElementById(colId), {
                group: "shared",
                animation: 150,
                onAdd: async function (evt) {
                    let taskId = evt.item.dataset.id;
                    let newStatus = evt.to.id;
                    let report = evt.item.dataset.report;

                    // cập nhật status trong DB
                    let formData = new FormData();
                    formData.append("task_id", taskId);
                    formData.append("status", newStatus);
                    formData.append("report_file", report);

                    const url = (window.APP_BASE_URL || '') + '/apps/Controllers/TaskController.php?action=changeStatus';
                    await fetch(url, {
                        method: "POST",
                        body: formData
                    });

                    // ⭐ sắp xếp lại tasks sau khi thả vào cột
                    sortColumnTasks(newStatus);
                }
            });
        });

        // User select change handler
        const userSelect = document.getElementById("userSelect");
        if (userSelect) {
            userSelect.addEventListener("change", async function () {
                let uid = this.value;

                if (!uid) return;

                let res = await fetch(
                    `${window.APP_BASE_URL || ''}/apps/Controllers/TaskController.php?action=getTasksByReceiver&user_id=${uid}`);
                let json = await res.json();

                if (!json.success) {
                    alert(json.message);
                    return;
                }

                let data = json.data;

                renderColumn("Pending", data.pending);
                renderColumn("In Progress", data.in_progress);
                renderColumn("Completed", data.completed);
                renderColumn("Overdue", data.overdue);

                function renderColumn(columnId, tasks) {
                    let col = document.getElementById(columnId);

                    // Giữ lại title ban đầu
                    let titleHtml = col.querySelector(".kanban-title").outerHTML;

                    // Clear nội dung cũ
                    col.innerHTML = titleHtml;

                    tasks.forEach(task => {
                        let div = document.createElement("div");
                        div.className = "task-card";
                        div.dataset.id = task.task_id;
                        div.dataset.title = task.title;
                        div.dataset.project = task.project_name;
                        div.dataset.createdby = task.full_name;
                        div.dataset.priority = task.priority;
                        div.dataset.deadline = task.deadline;
                        div.dataset.report = task.report_file ?? "";
                        div.dataset.desc = task.description;

                        div.onclick = function () {
                            openTaskModal(div);
                        };

                        // ⭐ LẤY LẠI CLASS THEO PRIORITY Y NHƯ BẢN GỐC
                        let priorityClass = "";
                        switch (task.priority) {
                            case "Low":
                                priorityClass = "priority-low";
                                break;
                            case "Medium":
                                priorityClass = "priority-medium";
                                break;
                            case "High":
                                priorityClass = "priority-high";
                                break;
                            case "Urgent":
                                priorityClass = "priority-urgent";
                                break;
                        }

                        div.innerHTML = `
    <strong>${task.title}</strong><br>
    <small class="text-muted">
        ${task.project_name} &nbsp;|&nbsp;
        <strong class="text-primary">${task.progress ?? 0}%</strong>
    </small>

    <div class="progress mt-1" style="height:6px;">
        <div class="progress-bar bg-success" style="width: ${task.progress ?? 0}%"></div>
    </div>

    <span class="badge ${priorityClass}">
        ${task.priority}
    </span>
`;

                        col.appendChild(div);
                    });

                    sortColumnTasks(columnId);
                }
            });
        }
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>