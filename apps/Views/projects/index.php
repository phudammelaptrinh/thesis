<?php
require_once __DIR__ . '/../../Controllers/ProjectController.php';
$controller = new ProjectController();
$projects = $controller->index();

//Lấy từ khóa tìm kiếm
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$status = isset($_GET['status']) ? trim($_GET['status']) : '';

$projects = $controller->searchProjects($keyword, $status);
?>

<?php include 'create.php'; ?>
<?php include 'edit.php'; ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-folder2"></i> Danh sách dự án</h5>
    </div>

    <div class="card-body">

        <!-- 🔍 Form tìm kiếm + nút thêm -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <form method="GET" action="index.php">
                <input type="hidden" name="page" value="projects">

                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên dự án"
                            value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                    </div>

                    <div class="col-md-6">
                        <select name="status" class="form-select">
                            <option value="">-- Tất cả trạng thái --</option>
                            <option value="Planning" <?= (isset($_GET['status']) && $_GET['status'] == 'Planning') ? 'selected' : '' ?>>
                                Planning</option>
                            <option value="Completed" <?= (isset($_GET['status']) && $_GET['status'] == 'Completed') ? 'selected' : '' ?>>
                                Completed</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>
                        </button>
                    </div>
                </div>
            </form>

            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addProjectModal">
                <i class="bi bi-plus-circle"></i> Thêm dự án
            </button>
        </div>

        <!-- Hiển thị thông báo -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
            <?php unset($_SESSION['success']); ?>
        <?php elseif (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- 🧾 Bảng danh sách dự án -->
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên dự án</th>
                    <th>Mô tả</th>
                    <th>Người nhận</th>
                    <th>Ngày bắt đầu</th>
                    <th>Ngày kết thúc</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($projects)): ?>
                    <?php foreach ($projects as $p): ?>
                        <tr>
                            <td><?= htmlspecialchars($p['project_id']) ?></td>
                            <td><?= htmlspecialchars($p['project_name']) ?></td>
                            <td><?= htmlspecialchars($p['description']) ?></td>
                            <td><?= htmlspecialchars($p['assigned_to_name'] ?? 'Chưa có người nhận') ?></td>
                            <td><?= htmlspecialchars($p['start_date']) ?></td>
                            <td><?= htmlspecialchars($p['end_date']) ?></td>
                            <td>
                                <span class="badge bg-<?= $p['status'] == 'Planning' ? 'success' : 'secondary' ?>">
                                    <?= htmlspecialchars($p['status']) ?>
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    onclick='openEditProjectModal(<?= json_encode($p, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)'>
                                    <i class="bi bi-pencil"></i>
                                </button>

                                <a href="index.php?page=delete_project&id=<?= $p['project_id'] ?>"
                                    onclick="return confirm('Bạn có chắc muốn xóa dự án này?');" class="btn btn-danger btn-sm">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted">Chưa có dự án nào</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    // ⭐ TÌM KIẾM REAL-TIME cho Projects
    document.addEventListener('DOMContentLoaded', () => {
        const searchForm = document.querySelector('form[method="GET"]');
        const keywordInput = searchForm ? searchForm.querySelector('input[name="keyword"]') : null;
        const statusSelect = searchForm ? searchForm.querySelector('select[name="status"]') : null;

        if (searchForm && keywordInput && statusSelect) {
            // Debounce function
            let searchTimeout;
            function debounceSearch() {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    searchForm.submit();
                }, 500);
            }

            // Tự động submit khi gõ
            keywordInput.addEventListener('input', debounceSearch);

            // Tự động submit khi chọn status
            statusSelect.addEventListener('change', () => {
                searchForm.submit();
            });
        }

        // 🔹 Khi bấm nút "Sửa" trên bảng
        window.openEditProjectModal = function (project) {
            document.getElementById('edit_project_id').value = project.project_id;
            document.getElementById('edit_project_name').value = project.project_name;
            document.getElementById('edit_description').value = project.description;
            document.getElementById('edit_Assignee').value = project.assigned_to;
            document.getElementById('edit_start_date').value = project.start_date;
            document.getElementById('edit_end_date').value = project.end_date;
            document.getElementById('edit_status').value = project.satus;

            const editProjectModalEl = document.getElementById('editProjectModal');
            if (editProjectModalEl) {
                const modal = new bootstrap.Modal(editProjectModalEl);
                modal.show();
            }
        };

        // 🔹 Tự đóng modal khi submit
        const editProjectForm = document.getElementById('editProjectForm');
        if (editProjectForm) {
            editProjectForm.addEventListener('submit', function () {
                const editProjectModalEl = document.getElementById('editProjectModal');
                if (editProjectModalEl) {
                    const modal = bootstrap.Modal.getInstance(editProjectModalEl);
                    if (modal) modal.hide();
                }
            });
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>