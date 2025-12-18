<?php
require_once __DIR__ . '/../../Controllers/AuthController.php';


// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Giao việc') {
    header("Location: " . url('apps/Views/auth/login.php'));
    exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
// $id = $_GET['project_id'] ?? null;
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Trang người giao việc | Taskbb</title>
    <link rel="icon" type="image/png" href="<?= asset('logo/logo.png') ?>">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            overflow-x: hidden;
        }

        /* Sidebar */
        #sidebar {
            width: 250px;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: #212529;
            color: white;
            transition: width 0.3s ease;
        }

        #sidebar.collapsed {
            width: 70px;
        }

        #sidebar .nav-link {
            color: white;
            padding: 12px 20px;
            transition: background 0.2s;
        }

        #sidebar .nav-link:hover {
            background: #343a40;
        }

        #sidebar.collapsed .nav-text {
            display: none;
        }

        #content {
            margin-left: 250px;
            transition: margin-left 0.3s ease;
        }

        #sidebar.collapsed+#content {
            margin-left: 70px;
        }
    </style>
</head>

<body>
    <!-- Header người giao việc -->
    <?php include('../layouts/header_ngv.php'); ?>

    <div id="content">
        <!-- Sidebar người giao việc -->
        <?php include('../layouts/sidebar_ngv.php'); ?>

        <div class="container-fluid mt-4">
            <?php
            switch ($page) {
                case 'projects':
                    include('../remote_ngv/projects.php');
                    break;
                case 'tasks':
                    include('../remote_ngv/tasks.php');
                    break;
                case 'reports':
                    include('../remote_ngv/reports.php');
                    break;
                case 'profile':
                    include('../remote_ngv/profile.php');
                    break;
                case 'review':
                    include('../remote_ngv/review.php');
                    break;
                case 'addTask':
                    include('../task/create.php');
                    break;
                case 'updateTask':
                    include('../task/edit.php');
                    break;
                case 'tasks_assigned':
                    include('../remote_ngv/task_assigned.php');
                    break;
                case 'history':
                    include('../task/history.php');
                    break;
                // case 'logout':
                //     $auth->logout();
                //     header('Location: taskbb/apps/Views/auth/login.php');
                //     exit();
                default:
                    echo "<h2>Trang không tồn tại</h2>";
                    break;
            }
            ?>
        </div>
    </div>

    <?php include('../auth/changepassword.php'); ?>
    <?php include('../auth/profile.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const sidebar = document.getElementById('sidebar');
            const toggleBtn = document.getElementById('sidebarToggle');

            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', () => {
                    sidebar.classList.toggle('collapsed');
                });
            }
        });
    </script>
</body>

</html>