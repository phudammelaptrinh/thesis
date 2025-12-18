<?php
require_once __DIR__ . '/../Models/ProjectModel.php';


class ProjectController
{
    private $model;


    public function __construct()
    {
        $this->model = new Project();
    }

    // Hiển thị tất cả dự án
    public function index()
    {
        return $this->model->getAllProjects();
    }

    // Tìm kiếm
    public function searchProjects($keyword, $status)
    {
        return $this->model->searchProjects($keyword, $status);
    }

    // Thêm mới
    public function addProject()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $project_name = $_POST['project_name'];
            $description = $_POST['description'] ?? '';
            $assigned_to = $_POST['assignee_name'] ?? null;
            $start_date = $_POST['start_date'];
            $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $progress = $_POST['progress'] ?? 0;
            $status = $_POST['status'];
            $this->model->addProject($project_name, $description, $assigned_to, $start_date, $end_date, $progress, $status);
            $_SESSION['success'] = "Thêm dự án thành công!";
            header("Location: ../Views/admin/index.php?page=projects");
            exit;
        }
    }

    // Lấy 1 dự án
    public function getProject($id)
    {
        return $this->model->getProjectById($id);
    }

    public function getProjectsByUser($userId)
    {
        return $this->model->getProjectsByUser($userId);
    }

    // Sửa dự án
    public function updateProject()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
            $project_id = $_POST['project_id'];
            $project_name = $_POST['project_name'];
            $description = $_POST['description'] ?? '';
            $assigned_to = $_POST['assignee_id'] ?? null;
            $start_date = $_POST['start_date'];
            $end_date = !empty($_POST['end_date']) ? $_POST['end_date'] : null;
            $progress = $_POST['progress'] ?? 0;
            $status = $_POST['status'];

            $success = $this->model->update_Project($project_id, $project_name, $description, $assigned_to, $start_date, $end_date, $progress, $status);
            if ($success) {
                $_SESSION['success'] = "Cập nhật dự án thành công!";
            } else {
                $_SESSION['error'] = "Cập nhật thất bại!";
            }

            header("Location: ../Views/admin/index.php?page=projects");
            exit;
        }
    }

    // Xóa dự án
    public function deleteProject($project_id)
    {
        $success = $this->model->deleteProject($project_id);
        if ($success) {
            $_SESSION['success'] = "Xóa dự án thành công!";
        } else {
            $_SESSION['error'] = "Xóa dự án thất bại!";
        }
    }

    public function assign()
    {
        header('Content-Type: application/json');
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['project_id'])) {
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            $userId = $_SESSION['user_id'] ?? null;
            $projectId = $_POST['project_id'];

            if (!$userId) {
                echo json_encode(['success' => false, 'message' => 'Bạn chưa đăng nhập!']);
                exit; // 🚀 Dừng lại ngay tại đây
            }

            // Lấy dự án để kiểm tra tiến độ
            $project = $this->model->getProjectById($projectId);
            if ($project && (int) $project['progress'] >= 100) {
                echo json_encode(['success' => false, 'message' => '❌ Dự án đã hoàn thành, không thể nhận!']);
                exit; // 🚀 Dừng tại đây
            }

            // Gán người giao việc
            $result = $this->model->assignProjectToUser($projectId, $userId);

            if ($result) {
                echo json_encode(['success' => true, 'message' => '✅ Nhận dự án thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật dữ liệu!']);
            }

            exit; // 🚀 Cực kỳ quan trọng: tránh PHP chạy tiếp
        } else {
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
            exit;
        }
    }
    public function unassignProject()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
            return;
        }

        $project_id = $_POST['project_id'] ?? null;
        if (!$project_id) {
            echo json_encode(['success' => false, 'message' => 'Thiếu ID dự án']);
            return;
        }

        $updated = $this->model->unassignProject($project_id);

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Hủy nhận dự án thành công!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể hủy nhận dự án!']);
        }
    }
}

// Gọi khi form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    $controller = new ProjectController();

    if ($action === 'add') {
        $controller->addProject();
    } elseif ($action === 'update') {
        $controller->updateProject();
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'assign') {
    $controller->assign();
}
if (isset($_GET['action']) && $_GET['action'] === 'unassign') {
    $controller->unassignProject();
}
