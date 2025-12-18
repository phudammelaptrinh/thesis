<?php
require_once __DIR__ . '/../../Controllers/ReportController.php';
$controller = new ReportController();
$data = $controller->index();

$stats     = $data['stats'] ?? [];
$assigners = $data['assigners'] ?? [];
$receivers = $data['receivers'] ?? [];

?>


<style>
.stat-box {
    color: #fff;
    padding: 18px;
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    transition: .25s;
}

.stat-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.25);
}

.stat-title {
    font-size: 14px;
    opacity: .9;
}

.stat-value {
    font-size: 30px;
    font-weight: bold;
}

#taskChart,
#projectChart {
    max-width: 250px !important;
    max-height: 250px !important;
    margin: 0 auto;
}
</style>


<div class="container mt-4">

    <h3 class="fw-bold mb-4">📊 Tổng quan hệ thống</h3>
    <div class="row g-3">

        <div class="col-md-3">
            <div class="stat-box" style="background:#007bff">
                <div class="stat-title">👤 Người dùng</div>
                <div class="stat-value"><?= $stats['total_users'] ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box" style="background:#17a2b8">
                <div class="stat-title">👤 Người giao việc</div>
                <div class="stat-value"><?= $stats['total_assigners'] ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box" style="background:#3399CC">
                <div class="stat-title">👤 Người nhận việc</div>
                <div class="stat-value"><?= $stats['total_receivers'] ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box" style="background:#6610f2">
                <div class="stat-title">📁 Dự án</div>
                <div class="stat-value"><?= $stats['total_projects'] ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box" style="background:#00b894">
                <div class="stat-title">✔ Dự án hoàn thành</div>
                <div class="stat-value"><?= $stats['total_projects_completed'] ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box" style="background:#fdcb6e">
                <div class="stat-title">🚧 Dự án đang làm</div>
                <div class="stat-value"><?= $stats['total_projects_processing'] ?></div>
            </div>
        </div>


        <div class="col-md-3">
            <div class="stat-box" style="background:#0984e3">
                <div class="stat-title">📦 Task</div>
                <div class="stat-value"><?= $stats['total_tasks'] ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box" style="background:#00cec9">
                <div class="stat-title">🏁 Task hoàn thành</div>
                <div class="stat-value"><?= $stats['total_tasks_completed'] ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box" style="background:#e17055">
                <div class="stat-title">⚙ Task đang làm</div>
                <div class="stat-value"><?= $stats['total_tasks_doing'] ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box" style="background:#636e72">
                <div class="stat-title">🗂 Task chưa làm</div>
                <div class="stat-value"><?= $stats['total_tasks_todo'] ?></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="stat-box" style="background:#d63031">
                <div class="stat-title">⛔ Task trễ hạn</div>
                <div class="stat-value"><?= $stats['total_tasks_late'] ?></div>
            </div>
        </div>

    </div>


    <!-- Chart -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Biểu đồ Task</h5>
                    <canvas id="taskChart"></canvas>
                </div>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <h5>Biểu đồ Project</h5>
                    <canvas id="projectChart"></canvas>
                </div>
            </div>
        </div>
    </div>


    <!-- Users -->
    <div class="row mt-5">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h5>👤 Người giao việc</h5>

                    <ul class="list-group mt-3">
                        <?php foreach ($assigners as $u): ?>
                        <li class="list-group-item">
                            <?= $u['full_name'] ?> — <?= $u['email'] ?> — <?= $u['status'] ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">

                    <h5>🧑 Người nhận việc</h5>

                    <ul class="list-group mt-3">
                        <?php foreach ($receivers as $u): ?>
                        <li class="list-group-item">
                            <?= $u['full_name'] ?> — <?= $u['email'] ?> — <?= $u['status'] ?>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
new Chart(document.getElementById('taskChart'), {
    type: 'doughnut',
    data: {
        labels: ['Đang làm', 'Hoàn thành', 'Trễ hạn'],
        datasets: [{
            data: [
                <?= $stats['total_tasks_doing'] ?>,
                <?= $stats['total_tasks_completed'] ?>,
                <?= $stats['total_tasks_late'] ?>
            ],
            backgroundColor: [
                '#e17055', // Đang làm
                '#00b894', // Hoàn thành
                '#d63031' // Trễ hạn
            ],
            hoverBackgroundColor: [
                '#d65d44',
                '#00a87f',
                '#c02927'
            ]
        }]
    }
});


new Chart(document.getElementById('projectChart'), {
    type: 'doughnut',
    data: {
        labels: ['Hoàn thành', 'Đang làm'],
        datasets: [{
            data: [
                <?= $stats['total_projects_completed'] ?>,
                <?= $stats['total_projects_processing'] ?>
            ],
            backgroundColor: [
                '#00b894', // Hoàn thành
                '#fdcb6e' // Đang làm
            ],
            hoverBackgroundColor: [
                '#00a87f',
                '#f5c259'
            ]
        }]
    }
});
</script>



<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>