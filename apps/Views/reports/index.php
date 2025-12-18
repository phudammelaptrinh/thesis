<?php
require_once __DIR__ . '/../../Controllers/ReportController.php';
require_once __DIR__ . '/../../Controllers/UserController.php';
require_once __DIR__ . '/../../Controllers/TaskController.php';
$controller = new ReportController();
$userCtrl = new UserController();
$taskCtrl = new TaskController();
// GET FILTER
$assignerId = $_GET['assigner'] ?? null;
$receiverId = $_GET['receiver'] ?? null;
// load filter data
$assigners = $userCtrl->getRoleUsers('Giao việc');
$receivers = $userCtrl->getRoleUsers('Nhận việc');
// load dashboard
$report_assigner = $controller->getSummary_assigner($assignerId);
$report_receiver = $controller->getSummary_receiver($receiverId);

$stats_assigner = $report_assigner['stats'] ?? [];
$stats_receiver = $report_receiver['stats'] ?? [];

$assignerInfo = null;
$receiverInfo = null;

if ($assignerId) {
    $assignerInfo = $userCtrl->getUserById($assignerId);
}
if ($receiverId) {
    $receiverInfo = $userCtrl->getUserById($receiverId);
}

$chart_data = [];

if ($assignerId) {
    $chart_data = [
        'Dự án' => (int) $stats_assigner['total_projects'],
        'Dự án đang thực hiện' => (int) $stats_assigner['total_projects_planning'],
        'Dự án hoàn thành' => (int) $stats_assigner['total_projects_completed'],
    ];
}

if ($receiverId) {
    $chart_data = [
        'Chưa làm' => (int) $stats_receiver['todo'],
        'Đang thực hiện' => (int) $stats_receiver['doing'],
        'Hoàn thành' => (int) $stats_receiver['completed'],
        'Trễ hạn' => (int) $stats_receiver['overdue'],
    ];
}

$exportName = '';
if ($assignerId && $assignerInfo) {
    $exportName = $assignerInfo['full_name'];
} elseif ($receiverId && $receiverInfo) {
    $exportName = $receiverInfo['full_name'];
}
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="m-0">📊 Bảng điều khiển giám sát & báo cáo</h2>
        <!-- EXPORT BUTTON -->
        <div class="d-flex justify-content-end mb-3">
            <button id="btnExportPdf" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Xuất báo cáo
            </button>
        </div>
    </div>
    <!-- bộ lọc -->
    <form method="GET" class="row g-3 mb-4">
        <input type="hidden" name="page" value="reports">
        <div class="col-md-4">
            <label class="form-label">Người giao việc</label>
            <select class="form-select" name="assigner">
                <option value="">-- Người giao việc --</option>
                <?php foreach ($assigners as $u): ?>
                    <option value="<?= $u['user_id'] ?>" <?= ($assignerId == $u['user_id'] ? 'selected' : '') ?>>
                        <?= htmlspecialchars($u['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label">Người nhận việc</label>
            <select class="form-select" name="receiver">
                <option value="">-- Người nhận việc --</option>
                <?php foreach ($receivers as $u): ?>
                    <option value="<?= $u['user_id'] ?>" <?= ($receiverId == $u['user_id'] ? 'selected' : '') ?>>
                        <?= htmlspecialchars($u['full_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button class="btn btn-primary w-100">Lọc dữ liệu</button>
        </div>
    </form>

    <?php if (!$assignerId && !$receiverId): ?>
        <div class="alert alert-warning text-center">
            Vui lòng chọn 1 người giao việc hoặc 1 người nhận việc để hiển thị báo cáo.
        </div>
    <?php else: ?>

        <div id="reportArea">
            <!-------------- ASSIGNER VIEW -------------->
            <?php if ($assignerId): ?>
                <h4 class="mb-3">
                    📌 Tổng quan người giao việc: <?= htmlspecialchars($assignerInfo['full_name'] ?? '') ?>
                </h4>
                <div class="row text-center">
                    <div class="col-md-4 mb-3">
                        <div class="card p-3 shadow-sm bg-primary text-white">
                            <h6>Dự án phụ trách</h6>
                            <h2><?= $stats_assigner['total_projects'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card p-3 shadow-sm bg-warning text-white">
                            <h6>Dự án đang thực hiện</h6>
                            <h2><?= $stats_assigner['total_projects_planning'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card p-3 shadow-sm bg-secondary text-white">
                            <h6>Dự án hoàn thành</h6>
                            <h2><?= $stats_assigner['total_projects_completed'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card p-3 shadow-sm bg-info text-white">
                            <h6>Task đã giao</h6>
                            <h2><?= $stats_assigner['total_tasks'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card p-3 shadow-sm bg-success text-white">
                            <h6>Tỷ lệ hoàn thành</h6>
                            <h2><?= $stats_assigner['avg_progress'] ?>%</h2>
                        </div>
                    </div>
                </div>
                <?php
                // Lấy task do người giao việc tạo
                if ($assignerId) {
                    $tasksByProject = $taskCtrl->getTasks_Assigner($assignerId);

                    // Nhóm theo project
                    $tasksGrouped = [];
                    foreach ($tasksByProject as $task) {
                        $projectName = $task['project_name'] ?? 'Không rõ dự án';
                        if (!isset($tasksGrouped[$projectName])) {
                            $tasksGrouped[$projectName] = [];
                        }
                        $tasksGrouped[$projectName][] = $task;
                    }
                    ?>
                    <!-- CHART -->
                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <div style="height:200px; width:400px; margin:auto;">
                                <canvas id="chartStatus"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="card shadow-sm mt-4">
                        <div class="card-body">
                            <h5 class="mb-3">📝 Các Task đã giao</h5>

                            <?php if (empty($tasksGrouped)): ?>
                                <div class="alert alert-info">Người này chưa giao task nào.</div>
                            <?php else: ?>
                                <?php foreach ($tasksGrouped as $projectName => $tasks): ?>
                                    <?php $progress = $tasks[0]['progress'] ?? 0; ?>
                                    <div class="mt-3">
                                        <div class="d-flex justify-content-between align-items-center mt-4">
                                            <h5 class="m-0">Dự án: <?= htmlspecialchars($projectName) ?></h5>
                                            <span class="badge bg-primary">Tiến độ: <?= $progress ?>%</span>
                                        </div>
                                        <table class="table table-bordered align-middle">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Tên Task</th>
                                                    <th>Người nhận</th>
                                                    <th>Trạng thái</th>
                                                    <th>Deadline</th>
                                                    <th>Báo cáo</th>
                                                    <th>Thao tác</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tasks as $task): ?>
                                                    <tr>
                                                        <td><?= htmlspecialchars($task['title']) ?></td>
                                                        <td><?= htmlspecialchars($task['assignee_name']) ?></td>
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
                                                        <td><?= htmlspecialchars($task['deadline']) ?></td>
                                                        <td>
                                                            <?php if (!empty($task['report_file'])): ?>
                                                                <a href="<?= VIEWS_UPLOAD_URL ?>/<?= urlencode($task['report_file']) ?>"
                                                                    target="_blank">
                                                                    <?= htmlspecialchars($task['report_file']) ?>
                                                                </a>
                                                            <?php else: ?>
                                                                <span class="text-muted">Chưa có file</span>
                                                            <?php endif; ?>
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
                    </div>
                <?php } ?>
                <!-------------- RECEIVER VIEW -------------->
            <?php elseif ($receiverId): ?>
                <?php
                // Lấy danh sách task theo người nhận
                $tasksReceiver = $taskCtrl->getTasks_Receiver($receiverId);

                // nhóm theo status
                $receiverGroups = [];
                foreach ($tasksReceiver as $task) {
                    $status = $task['status'] ?? 'Unknown';
                    if (!isset($receiverGroups[$status])) {
                        $receiverGroups[$status] = [];
                    }
                    $receiverGroups[$status][] = $task;
                }
                ?>
                <h4 class="mb-3">
                    📌 Tổng quan người nhận việc: <?= htmlspecialchars($receiverInfo['full_name'] ?? '') ?>
                </h4>
                <div class="row text-center">
                    <div class="col-md-3 mb-3">
                        <div class="card p-3 shadow-sm bg-info text-white">
                            <h6>Tổng task</h6>
                            <h2><?= $stats_receiver['total'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card p-3 shadow-sm bg-secondary text-white">
                            <h6>Chưa làm</h6>
                            <h2><?= $stats_receiver['todo'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card p-3 shadow-sm bg-primary text-white">
                            <h6>Đang làm</h6>
                            <h2><?= $stats_receiver['doing'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card p-3 shadow-sm bg-success text-white">
                            <h6>Hoàn thành</h6>
                            <h2><?= $stats_receiver['completed'] ?></h2>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card p-3 shadow-sm bg-danger text-white">
                            <h6>Trễ hạn</h6>
                            <h2><?= $stats_receiver['overdue'] ?></h2>
                        </div>
                    </div>
                </div>
                <!-- CHART -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <div style="height:200px; width:400px; margin:auto;">
                            <canvas id="chartStatus"></canvas>
                        </div>
                    </div>
                </div>
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="mb-3">📝 Các Task theo trạng thái</h5>

                        <?php if (empty($receiverGroups)): ?>
                            <div class="alert alert-info">Không có task nào.</div>
                        <?php else: ?>

                            <?php foreach ($receiverGroups as $status => $tasks): ?>
                                <div class="mt-3">

                                    <h6>
                                        <?= htmlspecialchars($status) ?> (<?= count($tasks) ?>)
                                    </h6>

                                    <table class="table table-bordered align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Tên Task</th>
                                                <th>Dự án</th>
                                                <th>Người giao</th>
                                                <th>Deadline</th>
                                                <th>Kết quả</th>
                                                <th>Thao tác</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($tasks as $task): ?>
                                                <tr>
                                                    <td><?= htmlspecialchars($task['title']) ?></td>
                                                    <td><?= htmlspecialchars($task['project_name']) ?></td>
                                                    <td><?= htmlspecialchars($task['assigner_name']) ?></td>
                                                    <td><?= htmlspecialchars($task['deadline']) ?></td>
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
                                            <?php endforeach ?>
                                        </tbody>
                                    </table>

                                </div>
                            <?php endforeach ?>

                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../task/detail.php' ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    const chartLabels = <?= json_encode(array_keys($chart_data)) ?>;
    const chartValues = <?= json_encode(array_values($chart_data)) ?>;

    // Màu theo vai trò
    let colors = [];

    <?php if ($assignerId): ?>
        colors = ['#0d6efd', '#ffc107', '#6c757d']; // Assigner
    <?php else: ?>
        colors = ['#6c757d', '#0d6efd', '#198754', '#dc3545']; // Receiver
    <?php endif; ?>

    new Chart(document.getElementById('chartStatus'), {
        type: 'bar',
        data: {
            labels: chartLabels,
            datasets: [{
                label: "Số lượng",
                data: chartValues,
                backgroundColor: colors,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Biểu đồ thống kê',
                    font: {
                        size: 16,
                        weight: 'bold',
                    },
                    padding: {
                        top: 10,
                        bottom: 20
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });


    document.getElementById('btnExportPdf')?.addEventListener('click', async () => {
        const el = document.getElementById('reportArea');

        try {
            // Kiểm tra thư viện
            if (typeof html2canvas === 'undefined') {
                throw new Error('html2canvas chưa được load');
            }
            if (typeof window.jspdf === 'undefined') {
                throw new Error('jsPDF chưa được load');
            }

            const canvas = await html2canvas(el, {
                scale: 2,
                useCORS: true,
                logging: false,
                allowTaint: true,
                backgroundColor: '#ffffff',
                windowWidth: el.scrollWidth,
                windowHeight: el.scrollHeight,
                ignoreElements: (element) => {
                    return element.classList?.contains('btn-close') ||
                        element.classList?.contains('dropdown-toggle');
                },
                onclone: (clonedDoc) => {
                    // Fix unsupported CSS colors
                    const allElements = clonedDoc.querySelectorAll('*');
                    allElements.forEach(elem => {
                        const computedStyle = window.getComputedStyle(elem);

                        if (computedStyle.backgroundColor && computedStyle.backgroundColor.includes('oklch')) {
                            elem.style.backgroundColor = '#f8f9fa';
                        }
                        if (computedStyle.color && computedStyle.color.includes('oklch')) {
                            elem.style.color = '#212529';
                        }
                        if (computedStyle.borderColor && computedStyle.borderColor.includes('oklch')) {
                            elem.style.borderColor = '#dee2e6';
                        }
                    });

                    const clonedArea = clonedDoc.getElementById('reportArea');
                    if (clonedArea) {
                        clonedArea.style.backgroundColor = '#ffffff';
                    }
                }
            });

            const img = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF('p', 'mm', 'a4');

            const pdfWidth = 190;
            const pdfHeight = (canvas.height * pdfWidth) / canvas.width;

            // Chia nhiều trang nếu cao quá
            const pageHeight = 277; // A4 height
            let heightLeft = pdfHeight;
            let position = 10;

            pdf.addImage(img, 'PNG', 10, position, pdfWidth, pdfHeight);
            heightLeft -= pageHeight;

            while (heightLeft > 0) {
                position = heightLeft - pdfHeight + 10;
                pdf.addPage();
                pdf.addImage(img, 'PNG', 10, position, pdfWidth, pdfHeight);
                heightLeft -= pageHeight;
            }

            const fileName = "BaoCao_<?= htmlspecialchars($exportName) ?>" + ".pdf";
            pdf.save(fileName);

        } catch (err) {
            console.error('Export error:', err);
            alert('Không thể xuất PDF. Vui lòng thử lại.\n' + err.message);
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const viewTaskModalEl = document.getElementById('viewTaskModal');
        const viewTaskModal = viewTaskModalEl ? new bootstrap.Modal(viewTaskModalEl) : null;

        window.openViewTaskModal = async function (taskId) {
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
        };

        // Gán sự kiện cho tất cả nút Xem
        document.querySelectorAll('.btn-view-task').forEach(btn => {
            btn.addEventListener('click', () => openViewTaskModal(btn.dataset.taskId));
        });
    });
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>