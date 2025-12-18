<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Controllers/UserController.php';
$userController = new UserController();
$currentUser = $userController->getUserWithRole($_SESSION['user_id'] ?? null);
?>

<!-- 🧩 MODAL HỒ SƠ CÁ NHÂN -->
<div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="profileModalLabel">
                    <i class="bi bi-person-circle"></i> Hồ sơ cá nhân
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Đóng"></button>
            </div>

            <form id="profileForm">
                <div class="modal-body">
                    <input type="hidden" name="user_id" value="<?= $currentUser['user_id'] ?? '' ?>">

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Họ và tên</label>
                            <input type="text" name="full_name" class="form-control"
                                value="<?= htmlspecialchars($currentUser['full_name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Ngày tạo tài khoản</label>
                            <input type="text" class="form-control bg-light"
                                value="<?= htmlspecialchars($currentUser['created_at'] ?? '') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Vai trò</label>
                            <input type="text" class="form-control bg-light"
                                value="<?= htmlspecialchars($currentUser['role_name'] ?? '') ?>" readonly>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle"></i> Đóng
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const profileForm = document.getElementById('profileForm');
        if (!profileForm) return;

        profileForm.addEventListener('submit', async function (e) {
            e.preventDefault();
            const formData = new FormData(this);
            formData.append('action', 'updateProfile');

            try {
                const url = (window.APP_BASE_URL || '') + '/apps/Controllers/AuthController.php?action=updateProfile';
                const res = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                const result = await res.json();
                if (result.success) {
                    alert('✅ Cập nhật thông tin thành công!');
                    location.reload();
                } else {
                    alert('❌ Lỗi: ' + (result.message || 'Không thể cập nhật'));
                }
            } catch (error) {
                console.error('Profile update error:', error);
                alert('❌ Lỗi kết nối server');
            }
        });
    });
</script>