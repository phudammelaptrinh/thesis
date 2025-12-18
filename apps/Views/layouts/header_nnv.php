<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../../Controllers/UserController.php';
$user = new UserController();

$currentUser = null;
if (isset($_SESSION['user_id'])) {
    $currentUser = $user->getUserById($_SESSION['user_id']);
}

$userName = $currentUser['full_name'] ?? 'Người nhận việc';
?>

<!-- 🔹 Thanh điều hướng -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold" href="<?= url('apps/Views/nnv/index.php') ?>">
            <img src="<?= asset('logo/logo.png') ?>" alt="Taskbb"
                style="height: 30px; vertical-align: middle; margin-right: 8px;">
        </a>

        <div class="d-flex align-items-center ms-auto">
            <!-- 🧪 Developer Mode Toggle -->
            <div class="me-4 d-flex align-items-center px-3 py-2 rounded"
                style="background: rgba(33, 37, 41, 0.5); border: 1px solid rgba(108, 117, 125, 0.3);">
                <span class="me-2" style="font-size: 0.85rem; color: #adb5bd;">
                    <i class="bi bi-code-slash"></i> Developer Mode
                </span>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" id="globalDevModeToggle"
                        style="cursor: pointer; width: 3rem; height: 1.5rem; background-color: #6c757d; border-color: #6c757d;">
                    <span id="devModeStatusBadge" class="badge ms-2"
                        style="min-width: 45px; background-color: #6c757d; color: #fff;">OFF</span>
                </div>
            </div>

            <!-- 👤 Thông tin người dùng -->
            <span class="text-light me-3">
                👋 Xin chào, <strong><?= htmlspecialchars($userName) ?></strong>
            </span>
        </div>
    </div>
</nav>

<!-- Bootstrap -->
<script>window.APP_BASE_URL = '<?= BASE_URL ?>';</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Global Dev Mode Script -->
<script>
    document.addEventListener('DOMContentLoaded', async function () {
        const toggle = document.getElementById('globalDevModeToggle');
        const badge = document.getElementById('devModeStatusBadge');

        if (!toggle || !badge) return;

        // Kiểm tra trạng thái ban đầu
        async function checkDevMode() {
            try {
                const url = (window.APP_BASE_URL || '') + '/apps/Controllers/TaskController.php';
                const formData = new FormData();
                formData.append('action', 'checkDevMode');

                const res = await fetch(url, { method: 'POST', body: formData });
                const json = await res.json();

                console.log('🔍 Dev Mode Status:', json);

                if (json.success && json.enabled) {
                    toggle.checked = true;
                    toggle.style.backgroundColor = '#dc3545';
                    toggle.style.borderColor = '#dc3545';
                    badge.textContent = 'ON';
                    badge.style.backgroundColor = '#dc3545';
                    badge.style.color = '#fff';
                    localStorage.setItem('devMode', '1');
                    console.log('✅ Dev Mode: ENABLED');
                } else {
                    toggle.checked = false;
                    toggle.style.backgroundColor = '#6c757d';
                    toggle.style.borderColor = '#6c757d';
                    badge.textContent = 'OFF';
                    badge.style.backgroundColor = '#6c757d';
                    badge.style.color = '#fff';
                    localStorage.setItem('devMode', '0');
                    console.log('🔒 Dev Mode: DISABLED');
                }
            } catch (err) {
                console.error('❌ Failed to check dev mode:', err);
            }
        }

        // Toggle dev mode
        toggle.addEventListener('change', async function () {
            const isEnabled = this.checked;

            try {
                const url = (window.APP_BASE_URL || '') + '/apps/Controllers/TaskController.php';
                const formData = new FormData();
                formData.append('action', 'toggleDevMode');
                formData.append('enable', isEnabled ? 'true' : 'false');

                const res = await fetch(url, { method: 'POST', body: formData });
                const json = await res.json();

                if (!json.success) {
                    alert('❌ Lỗi: ' + json.message);
                    this.checked = !isEnabled;
                    return;
                }

                if (isEnabled) {
                    toggle.style.backgroundColor = '#dc3545';
                    toggle.style.borderColor = '#dc3545';
                    badge.textContent = 'ON';
                    badge.style.backgroundColor = '#dc3545';
                    badge.style.color = '#fff';
                    localStorage.setItem('devMode', '1');

                    // Hiển thị thông báo nhỏ gọn
                    const toast = document.createElement('div');
                    toast.className = 'position-fixed bottom-0 end-0 p-3';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = `
                    <div class="toast show" role="alert" style="background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%); color: white; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <div class="toast-header" style="background: rgba(0,0,0,0.1); color: white; border-bottom: 1px solid rgba(255,255,255,0.2);">
                            <strong class="me-auto">⚠️ Dev Mode ENABLED</strong>
                            <button type="button" class="btn-close btn-close-white" onclick="this.closest('.position-fixed').remove()"></button>
                        </div>
                        <div class="toast-body">
                            <i class="bi bi-shield-x me-1"></i> Upload mọi file types, không scan malware
                        </div>
                    </div>
                `;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 5000);
                } else {
                    toggle.style.backgroundColor = '#6c757d';
                    toggle.style.borderColor = '#6c757d';
                    badge.textContent = 'OFF';
                    badge.style.backgroundColor = '#6c757d';
                    badge.style.color = '#fff';
                    localStorage.setItem('devMode', '0');

                    const toast = document.createElement('div');
                    toast.className = 'position-fixed bottom-0 end-0 p-3';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = `
                    <div class="toast show" role="alert" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; border: none; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                        <div class="toast-header" style="background: rgba(0,0,0,0.1); color: white; border-bottom: 1px solid rgba(255,255,255,0.2);">
                            <strong class="me-auto">✅ Dev Mode DISABLED</strong>
                            <button type="button" class="btn-close btn-close-white" onclick="this.closest('.position-fixed').remove()"></button>
                        </div>
                        <div class="toast-body">
                            <i class="bi bi-shield-check me-1"></i> Production security đã được khôi phục
                        </div>
                    </div>
                `;
                    document.body.appendChild(toast);
                    setTimeout(() => toast.remove(), 5000);
                }
            } catch (err) {
                console.error('Failed to toggle dev mode:', err);
                alert('❌ Lỗi khi toggle dev mode!');
                this.checked = !isEnabled;
            }
        });

        // Load trạng thái khi khởi động
        await checkDevMode();
    });

    // Helper function để các trang con kiểm tra dev mode
    window.isDevMode = function () {
        return localStorage.getItem('devMode') === '1';
    };
</script>