<?php
if (session_status() === PHP_SESSION_NONE)
    session_start();
?>

<!-- Modal Đổi Mật Khẩu -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="bi bi-key me-2"></i>Đổi mật khẩu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>

            <div class="modal-body">
                <form id="changePasswordForm">
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mật khẩu mới</label>
                        <input type="password" name="new_password" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Xác nhận mật khẩu mới</label>
                        <input type="password" name="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cập nhật mật khẩu</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("changePasswordForm");
        const modalEl = document.getElementById("changePasswordModal");
        if (!form || !modalEl) return;

        form.addEventListener("submit", async function (e) {
            e.preventDefault();

            const formData = new FormData(form);
            formData.append("action", "changePassword");

            if (formData.get("new_password") !== formData.get("confirm_password")) {
                alert("Mật khẩu mới và xác nhận không khớp!");
                return;
            }

            try {
                const url = (window.APP_BASE_URL || '') + '/apps/Controllers/AuthController.php';
                const res = await fetch(url, {
                    method: "POST",
                    body: formData
                });

                const text = await res.text();
                let data;
                try {
                    data = JSON.parse(text);
                } catch {
                    alert("Server trả về dữ liệu không hợp lệ: " + text);
                    return;
                }

                alert(data.message);

                if (data.success) {
                    form.reset();

                    // ⭐ FIX: Lắng nghe sự kiện modal đóng hoàn tất, SAU ĐÓ mới reload
                    const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);

                    // Listen to 'hidden.bs.modal' - kích hoạt SAU KHI modal đã đóng hoàn toàn
                    modalEl.addEventListener('hidden.bs.modal', function onModalHidden() {
                        // Remove listener để tránh trigger nhiều lần
                        modalEl.removeEventListener('hidden.bs.modal', onModalHidden);

                        // Force clean up backdrop (phòng trường hợp Bootstrap không tự dọn)
                        document.querySelectorAll('.modal-backdrop').forEach(el => el.remove());
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                        document.body.style.paddingRight = '';

                        // Reload page
                        location.reload();
                    });

                    // Trigger đóng modal
                    modal.hide();
                }
            } catch (err) {
                alert("Xin hãy đăng nhập lại!");
                console.error(err);
            }
        });
    });
</script>