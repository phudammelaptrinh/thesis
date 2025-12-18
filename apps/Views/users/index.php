<?php
require_once __DIR__ . '/../../Controllers/UserController.php';
$controller = new UserController();
$usersCtrl_Roles = $controller->getAllRoles();

//Xử lý tìm kiếm
$keyword = $_GET['keyword'] ?? '';
$role_id = $_GET['role_id'] ?? '';
$users = $controller->searchUsers($keyword, $role_id);

?>

<?php include 'create.php'; ?>
<?php include 'edit.php'; ?>

<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="bi bi-people"></i> Danh sách người dùng</h5>
    </div>
    <div class="card-body">

        <!-- Form tìm kiếm + nút thêm -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <form method="GET" action="index.php" class="d-flex align-items-center" style="gap: 8px;">
                <input type="hidden" name="page" value="users">

                <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>"
                    class="form-control form-control-sm" placeholder="Tìm theo tên hoặc email...">

                <select name="role_id" class="form-select form-select-sm">
                    <option value="">-- Vai trò --</option>
                    <option value="1" <?= isset($_GET['role_id']) && $_GET['role_id'] == '1' ? 'selected' : '' ?>>Admin
                    </option>
                    <option value="2" <?= isset($_GET['role_id']) && $_GET['role_id'] == '2' ? 'selected' : '' ?>>Giao
                        việc</option>
                    <option value="3" <?= isset($_GET['role_id']) && $_GET['role_id'] == '3' ? 'selected' : '' ?>>Nhận
                        việc</option>
                </select>
                <button type="submit" class="btn btn-primary btn-sm d-flex align-items-center px-3">
                    <i class="bi bi-search me-1"></i>
                </button>
            </form>
            <!-- Nút mở modal -->
            <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="bi bi-plus-circle"></i> Thêm người dùng
            </button>
        </div>

    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
        <?php unset($_SESSION['success']); ?>
    <?php elseif (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <table class="table table-bordered table-hover align-middle">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Tên người dùng</th>
                <th>Email</th>
                <th>Vai trò</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Ngày cập nhật</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($users)): ?>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['user_id']) ?></td>
                        <td><?= htmlspecialchars($u['full_name']) ?></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['role']) ?></td>
                        <td>
                            <span class="badge bg-<?= $u['status'] == 'active' ? 'success' : 'secondary' ?>">
                                <?= htmlspecialchars($u['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($u['created_at']) ?></td>
                        <td><?= htmlspecialchars($u['updated_at']) ?></td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-btn" data-id="<?= $u['user_id']; ?>"
                                data-name="<?= htmlspecialchars($u['full_name']); ?>"
                                data-email="<?= htmlspecialchars($u['email']); ?>" data-role="<?= $u['role']; ?>"
                                data-status="<?= $u['status']; ?>" data-bs-toggle="modal" data-bs-target="#editUserModal">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <a href="index.php?page=delete_user&id=<?= $u['user_id'] ?>"
                                onclick="return confirm('Bạn có chắc chắn muốn xóa người dùng này?');"
                                class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center text-muted">Chưa có người dùng nào</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; ?></div>
    <?php unset($_SESSION['success']); ?>
<?php elseif (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('#addUserModal form');

        if (form) {
            form.addEventListener('submit', function (e) {
                // Không chặn submit (để PHP xử lý), chỉ đóng modal trước khi reload
                const modalElement = document.getElementById('addUserModal');
                const modalInstance = bootstrap.Modal.getInstance(modalElement);

                if (modalInstance) {
                    modalInstance.hide();
                }
            });
        }

        // ⭐ TÌM KIẾM REAL-TIME
        const searchForm = document.querySelector('form[method="GET"]');
        const keywordInput = searchForm ? searchForm.querySelector('input[name="keyword"]') : null;
        const roleSelect = searchForm ? searchForm.querySelector('select[name="role_id"]') : null;

        // Debounce function để tránh search quá nhiều lần
        let searchTimeout;
        function debounceSearch() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                searchForm.submit();
            }, 500); // Đợi 500ms sau khi ngừng gõ
        }

        // Tự động submit khi gõ vào ô tìm kiếm
        if (keywordInput) {
            keywordInput.addEventListener('input', debounceSearch);
        }

        // Tự động submit khi chọn vai trò
        if (roleSelect) {
            roleSelect.addEventListener('change', () => {
                searchForm.submit();
            });
        }
    });
</script>

<script>
    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit_user_id').value = this.dataset.id;
            document.getElementById('edit_name').value = this.dataset.name;
            document.getElementById('edit_email').value = this.dataset.email;

            // Gán giá trị cho vai trò và trạng thái
            document.getElementById('edit_role_id').value = this.dataset.role_id;
            document.getElementById('edit_status').value = this.dataset.status;
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>