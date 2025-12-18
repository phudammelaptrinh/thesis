<?php
require_once __DIR__ . '/../../Controllers/UserController.php';
require_once __DIR__ . '/../../Controllers/ProjectController.php';
require_once __DIR__ . '/../../Controllers/AuthController.php';


// Kiểm tra quyền truy cập
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
  header("Location: " . url('apps/Views/auth/login.php'));
  exit;
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

// ✅ Xử lý xóa project trước khi gửi HTML
if ($page === 'delete_project' && isset($_GET['id'])) {
  $projects = new ProjectController();
  $projects->deleteProject((int) $_GET['id']);
  header("Location: index.php?page=projects");
  exit; // kết thúc script ngay sau redirect
}

// ✅ Xử lý xóa user trước khi gửi HTML
if ($page === 'delete_user' && isset($_GET['id'])) {
  $user = new UserController();
  $user->deleteUser((int) $_GET['id']);
  header("Location: index.php?page=users");
  exit;
}
?>

<!DOCTYPE html>
<html lang="vi">

<head>
  <meta charset="UTF-8">
  <title>Trang quản trị | Taskbb</title>
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

    .sidebar-toggle {
      cursor: pointer;
    }
  </style>
</head>

<body>
  <?php include('../layouts/header_admin.php'); ?>

  <div id="content" style="margin-left:250px;">
    <?php include('../layouts/sidebar_admin.php'); ?>

    <div class="container-fluid mt-4">
      <?php
      switch ($page) {
        case 'profile':
          include('../remote_admin/profile.php');
          break;
        case 'generals':
          include('../reports/general.php');
          break;
        case 'users':
          include('../users/index.php');
          break;
        case 'projects':
          include('../projects/index.php');
          break;
        case 'reports':
          require_once __DIR__ . '/../../Controllers/ReportController.php';
          $controller = new ReportController();
          $report = $controller->index(); // Lấy dữ liệu từ controller
      
          // Bao gồm view để render HTML
          include(__DIR__ . '/../reports/index.php');
          break;
        case 'logout':
          $auth->logout();
          break;
        case 'add_user':
          include('../users/create.php');
          break;
        case 'add_project':
          include('../projects/create.php');
          break;
        case 'edit_user':
          include('../users/edit.php');
          break;
        case 'delete_user':
          if (isset($_GET['id'])) {
            $userModel = new User();
            $userModel->deleteUser($_GET['id']);
          }
          header("Location: index.php?page=users");
          break;
        case 'edit_project':
          include('../projects/edit.php');
          break;
        case 'delete_project':
          if (isset($_GET['id'])) {
            $projects = new Project();
            $projects->deleteProject($_GET['id']);
          }
          header("Location: " . url('apps/Views/admin/index.php?page=projects'));
          break;
        case 'profile':
          include('../remote_admin/profile.php');
          break;
        case 'projects_assigned':
          include('../remote_ngv/projects.php');
          break;
        case 'tasks':
          include('../remote_admin/tasks.php');
          break;
        case 'tasks_review':
          include('../remote_ngv/review.php');
          break;
        case 'reports_ngv':
          include('../remote_ngv/reports.php');
          break;
        case 'tasks_assigned':
          include('../remote_admin/task_assigned.php');
          break;
        case 'history':
          include('../remote_admin/history.php');
          break;
        default:
          echo "<h2>Trang không tồn tại</h2>";
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