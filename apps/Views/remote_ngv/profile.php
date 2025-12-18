<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
require_once __DIR__ . '/../../Controllers/UserController.php';
$userController = new UserController();

// Lấy thông tin người dùng hiện tại
$currentUser = $userController->getUserWithRole($_SESSION['user_id'] ?? null);

if (!$currentUser) {
    echo '<div class="alert alert-danger text-center py-5">Không tìm thấy thông tin người dùng hoặc chưa đăng nhập!</div>';
    exit;
}
?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Hồ sơ cá nhân</h5>
                </div>
                <div class="card-body">
                    <form id="profileForm">
                        <input type="hidden" name="user_id" value="<?= $currentUser['user_id'] ?>">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên</label>
                                <input type="text" name="full_name" class="form-control"
                                    value="<?= htmlspecialchars($currentUser['full_name']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control"
                                    value="<?= htmlspecialchars($currentUser['email']) ?>" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Ngày tạo tài khoản</label>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($currentUser['created_at']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Vai trò</label>
                                <input type="text" class="form-control"
                                    value="<?= htmlspecialchars($currentUser['role_name']) ?>" readonly>
                            </div>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu thay
                                đổi</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    input[readonly] {
        background-color: #f8f9fa;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const profileForm = document.getElementById('profileForm');
        if (!profileForm) return;

        profileForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            formData.append('action', 'updateProfile');

            try {
                const url = (window.APP_BASE_URL || '') + '/apps/Controllers/AuthController.php?action=updateProfile';
                const res = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                if (!res.ok) throw new Error('Không thể gửi yêu cầu');

                const data = await res.json();

                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Lỗi: ' + data.message);
                }
            } catch (err) {
                console.error(err);
                alert('Đã xảy ra lỗi khi cập nhật hồ sơ.');
            }
        });
    });
</script>