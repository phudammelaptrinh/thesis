<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// $stats ban đầu có thể rỗng, UI sẽ load qua JS
?>
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold"><i class="bi bi-bar-chart-line me-2 text-primary"></i> Báo cáo dự án</h4>

        <div class="d-flex gap-2 align-items-center">
            <select id="projectSelect" class="form-select form-select-sm">
            </select>
            <button id="btnExportPdf" class="btn btn-outline-danger btn-sm">
                <i class="bi bi-file-earmark-pdf"></i> Export
            </button>
        </div>
    </div>

    <div id="reportArea">
        <div class="row g-3 mb-4 text-center" id="statsRow">
            <!-- cards sẽ được render bằng JS -->
        </div>

        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <strong>Biểu đồ công việc theo ngày</strong>
            </div>
            <div class="card-body">
                <canvas id="tasksChart" height="120"></canvas>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header">
                <strong>Chi tiết</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Loại</th>
                            <th>Số lượng</th>
                        </tr>
                    </thead>
                    <tbody id="detailTableBody">
                        <!-- nội dung sẽ do JS fill -->
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card shadow-sm mt-4">
            <div class="card-header">
                <strong>Công việc theo trạng thái</strong>
            </div>
            <div class="card-body">
                <table class="table table-sm" id="tblTasks">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Tên task</th>
                            <th>Người nhận</th>
                            <th>Deadline</th>
                            <th>Trạng thái</th>
                            <th>Báo cáo</th>
                            <th>Kết quả</th>
                        </tr>
                    </thead>
                    <tbody id="taskListBody"></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- MODAL DETAIL -->
<div class="modal fade" id="taskDetailModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết công việc</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="taskDetailBody">
                <!-- dữ liệu sẽ load bằng JS -->
            </div>
        </div>
    </div>
</div>

<!-- CDN Chart.js, html2canvas, jsPDF -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

<script>
    (async function initReports() {
        const projectSelect = document.getElementById('projectSelect');
        const statsRow = document.getElementById('statsRow');
        const detailBody = document.getElementById('detailTableBody');
        const ctx = document.getElementById('tasksChart').getContext('2d');
        let chart;

        // Lấy danh sách project
        async function loadProjects() {
            const url = (window.APP_BASE_URL || '') + '/apps/Controllers/ReportController.php?action=listProjects';
            const res = await fetch(url);
            const json = await res.json();
            if (!json.success) return;
            projectSelect.innerHTML = '-- Chọn dự án --';
            json.data.forEach(p => {
                const opt = document.createElement('option');
                opt.value = p.project_id;
                opt.textContent = p.project_name;
                projectSelect.appendChild(opt);
            });

            // chọn project đầu tiên nếu có
            if (projectSelect.options.length > 0) {
                projectSelect.value = projectSelect.options[0].value;
                await loadStats(projectSelect.value);
            }
        }

        // Lấy stats cho project
        async function loadStats(projectId) {
            const res = await fetch(
                `${window.APP_BASE_URL || ''}/apps/Controllers/ReportController.php?action=projectStats&project_id=${projectId}`
            );
            const json = await res.json();
            if (!json.success) {
                alert(json.message || 'Không lấy được thống kê');
                return;
            }

            const payload = json.data;
            renderSummary(payload);
            renderDetails(payload);
            renderChart(payload);
        }

        function renderSummary(payload) {
            const s = payload.stats;
            statsRow.innerHTML = '';

            const cards = [{
                label: 'Tổng công việc',
                value: s.total_tasks ?? 0,
                cls: 'text-primary'
            },
            {
                label: 'Đã giao',
                value: s.assigned ?? 0,
                cls: 'text-warning'
            },
            {
                label: 'Chưa làm',
                value: (s.by_status['Pending'] ?? 0),
                cls: 'text-secondary'
            },
            {
                label: 'Đang làm',
                value: (s.by_status['In Progress'] ?? 0),
                cls: 'text-info'
            },
            {
                label: 'Hoàn thành',
                value: (s.by_status['Completed'] ?? 0),
                cls: 'text-success'
            },
            {
                label: `Trễ hạn <span class="text-muted">`,
                value: (s.by_status['Overdue'] ?? 0),
                cls: 'text-danger'
            },
            {
                label: 'Tiến độ',
                value: (s.progress ?? 0) + '%',
                cls: 'text-success'
            }
            ];

            cards.forEach(c => {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 col-lg-2';
                col.innerHTML = `
            <div class="card shadow-sm border-0 text-center h-100">
                <div class="card-body">
                    <h6 class="text-muted">${c.label}</h6>
                    <h2 class="${c.cls} fw-bold mb-0">${c.value}</h2>
                </div>
            </div>
        `;
                statsRow.appendChild(col);
            });
        }


        function renderDetails(payload) {
            detailBody.innerHTML = '';
            const byStatus = payload.stats.by_status || {};
            const rows = [
                ['Tổng công việc', payload.stats.total_tasks || 0],
                ['Đã giao', payload.stats.assigned || 0],
                ['Pending', byStatus['Pending'] || 0],
                ['In Progress', byStatus['In Progress'] || 0],
                ['Completed', byStatus['Completed'] || 0],
                ['Overdue', byStatus['Overdue'] || 0]
            ];
            rows.forEach(r => {
                const tr = document.createElement('tr');
                tr.innerHTML = `<td>${r[0]}</td><td>${r[1]}</td>`;
                detailBody.appendChild(tr);
            });
        }

        function renderChart(payload) {
            // prepare timeseries 30 days
            const ts = payload.stats.timeseries || [];
            const map = {};
            ts.forEach(item => map[item.dt] = item.cnt);

            // build labels last 30 days
            const labels = [];
            const data = [];
            for (let i = 29; i >= 0; i--) {
                const d = new Date();
                d.setDate(d.getDate() - i);
                const iso = d.toISOString().slice(0, 10);
                labels.push(iso);
                data.push(map[iso] ? parseInt(map[iso]) : 0);
            }

            if (chart) {
                chart.data.labels = labels;
                chart.data.datasets[0].data = data;
                chart.update();
                return;
            }

            chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Số task tạo',
                        data: data,
                        tension: 0.3,
                        fill: true,
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            display: true
                        },
                        y: {
                            beginAtZero: true,
                            precision: 0
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
        }

        // Export PDF
        document.getElementById('btnExportPdf').addEventListener('click', async () => {
            const area = document.getElementById('reportArea');

            try {
                // Kiểm tra thư viện có load không
                if (typeof html2canvas === 'undefined') {
                    throw new Error('html2canvas chưa được load');
                }
                if (typeof window.jspdf === 'undefined') {
                    throw new Error('jsPDF chưa được load');
                }

                const canvas = await html2canvas(area, {
                    scale: 2,
                    useCORS: true,
                    logging: false,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    windowWidth: area.scrollWidth,
                    windowHeight: area.scrollHeight,
                    ignoreElements: (element) => {
                        // Bỏ qua elements có thể gây lỗi
                        return element.classList?.contains('btn-close') ||
                            element.classList?.contains('dropdown-toggle');
                    },
                    onclone: (clonedDoc) => {
                        // Fix unsupported CSS colors
                        const allElements = clonedDoc.querySelectorAll('*');
                        allElements.forEach(el => {
                            const computedStyle = window.getComputedStyle(el);

                            // Replace oklch/oklab colors with fallback
                            if (computedStyle.backgroundColor && computedStyle.backgroundColor.includes('oklch')) {
                                el.style.backgroundColor = '#f8f9fa';
                            }
                            if (computedStyle.color && computedStyle.color.includes('oklch')) {
                                el.style.color = '#212529';
                            }
                            if (computedStyle.borderColor && computedStyle.borderColor.includes('oklch')) {
                                el.style.borderColor = '#dee2e6';
                            }
                        });

                        const clonedArea = clonedDoc.getElementById('reportArea');
                        if (clonedArea) {
                            clonedArea.style.backgroundColor = '#ffffff';
                        }
                    }
                });

                const imgData = canvas.toDataURL('image/png');
                const { jsPDF } = window.jspdf;
                const pdf = new jsPDF('p', 'mm', 'a4');

                const imgProps = pdf.getImageProperties(imgData);
                const pdfWidth = 190;
                const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

                // Nếu cao quá, chia thành nhiều trang
                const pageHeight = 277;
                let heightLeft = pdfHeight;
                let position = 10;

                pdf.addImage(imgData, 'PNG', 10, position, pdfWidth, pdfHeight);
                heightLeft -= pageHeight;

                while (heightLeft > 0) {
                    position = heightLeft - pdfHeight + 10;
                    pdf.addPage();
                    pdf.addImage(imgData, 'PNG', 10, position, pdfWidth, pdfHeight);
                    heightLeft -= pageHeight;
                }

                const projectName = projectSelect.options[projectSelect.selectedIndex]?.text || 'all';
                pdf.save(`BaoCao_${projectName}.pdf`);

            } catch (err) {
                console.error('Export error:', err);
                alert('Không thể xuất PDF. Vui lòng thử lại.\n' + err.message);
            }
        });

        async function loadTasks(projectId) {
            const res = await fetch(
                `${window.APP_BASE_URL || ''}/apps/Controllers/ReportController.php?action=projectTasks&project_id=${projectId}`);
            const json = await res.json();
            if (!json.success) return;

            const list = json.data;
            const body = document.getElementById("taskListBody");
            body.innerHTML = '';

            list.forEach((t, i) => {
                const fileUrl = t.report_file ?
                    (window.APP_BASE_URL || '') + `/Views/uploads/${encodeURIComponent(t.report_file)}` : null;
                const reportCell = fileUrl ?
                    `<a href="${fileUrl}" target="_blank">${t.report_file}</a>` :
                    `<span class="text-muted">Chưa có file</span>`;

                const resultCell = t.result ?? 'Chưa có kết quả';

                const tr = document.createElement('tr');
                tr.innerHTML = `
            <td>${i + 1}</td>
            <td>${t.title}</td>
            <td>${t.assignee ?? ''}</td>
            <td>${t.deadline ?? ''}</td>
            <td>${t.status}</td>
            <td>${reportCell}</td>
            <td>${resultCell}</td>
        `;
                body.appendChild(tr);
            });
        }

        // change project
        projectSelect.addEventListener('change', () => {
            loadStats(projectSelect.value);
            loadTasks(projectSelect.value);
        });

        // init
        await loadProjects();
        await loadTasks(projectSelect.value);
    })();
</script>

<style>
    /* nhỏ gọn cho card */
    #statsRow .card {
        border-radius: 10px;
    }

    #reportArea {
        padding-bottom: 40px;
    }
</style>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>