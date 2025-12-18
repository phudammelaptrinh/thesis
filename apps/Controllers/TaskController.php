<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Models/TaskModel.php';
require_once __DIR__ . '/../Models/ProjectModel.php';
require_once __DIR__ . '/cUpload.php';

class TaskController
{
    private $taskModel;
    private $projectModel;

    public function __construct()
    {
        $this->taskModel = new Task();
        $this->projectModel = new Project();
    }

    // ✅ Lấy danh sách công việc theo project_id
    public function index()
    {
        $projectId = $_GET['project_id'] ?? null;

        if (!$projectId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu mã dự án!']);
            return;
        }

        $tasks = $this->taskModel->getTasksByProjectId($projectId);
        echo json_encode(['success' => true, 'data' => $tasks]);
    }

    public function getTasks_Project($projectId)
    {
        return $this->taskModel->getTasks_ProjectId($projectId);
    }

    public function getTasks_Receiver($receiverId)
    {
        return $this->taskModel->getTasks_Receiver($receiverId);
    }

    public function getTasksByProjectId($projectId)
    {
        return $this->taskModel->getTasksByProjectId($projectId);
    }

    public function getTasks_Assigner($userId)
    {
        return $this->taskModel->getTasks_Assigner($userId);
    }

    public function getTaskById($taskId)
    {
        return $this->taskModel->getTaskById($taskId);
    }

    public function getTasks_ID($taskId)
    {
        return $this->taskModel->getTasks_ID($taskId);
    }

    public function getTasksByUser($userID)
    {
        return $this->taskModel->getTasksByUser($userID);
    }

    public function getCompletedTasksByUser($user_id)
    {
        return $this->taskModel->getCompletedTasksByUser($user_id);
    }

    // Thêm công việc mới
    public function addTask()
    {
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $project_id = $_POST['project_id'] ?? null;
        $assigned_to = $_POST['assignee_id'] ?? null;
        $deadline = $_POST['deadline'] ?? null;
        $created_by = $_SESSION['user_id'] ?? null;
        $priority = $_POST['priority'] ?? null;


        // Kiểm tra dữ liệu cơ bản
        if (!$title || !$project_id || !$created_by || !$assigned_to || !$deadline) {
            return $this->jsonResponse(false, 'Thiếu thông tin cần thiết để tạo công việc.');
        }

        // Lấy thông tin project để ràng buộc thời gian
        $project = $this->projectModel->getProjectById($project_id);

        if (!$project) {
            return $this->jsonResponse(false, 'Dự án không tồn tại.');
        }

        $start_date = $project['start_date'] ?? null;
        $end_date = $project['end_date'] ?? null;

        // Kiểm tra hạn hoàn thành phải nằm trong khoảng ngày của dự án
        if ($start_date && $end_date) {
            if ($deadline < $start_date || $deadline > $end_date) {
                return $this->jsonResponse(false, "Hạn hoàn thành phải nằm trong khoảng từ $start_date đến $end_date của dự án.");
            }
        }
        // Gọi model để thêm task
        $result = $this->taskModel->addTask($title, $description, $project_id, $created_by, $assigned_to, $deadline, $priority);

        if ($result) {
            return $this->jsonResponse(true, 'Tạo công việc thành công!');
        } else {
            return $this->jsonResponse(false, 'Lỗi khi tạo công việc!');
        }
    }

    // Cập nhật công việc
    public function updateTask()
    {
        $taskId = $_POST['task_id'] ?? null;
        $title = $_POST['title'] ?? '';
        $description = $_POST['description'] ?? '';
        $assigneeId = $_POST['assignee_id'] ?? null;
        $deadline = $_POST['deadline'] ?? '';
        $priority = $_POST['priority'] ?? null;
        $status = $_POST['status'] ?? 'Pending';

        if (!$taskId || !$title || !$description || !$assigneeId || !$deadline) {
            $this->jsonResponse(false, 'Thiếu dữ liệu bắt buộc');
        }

        // Lấy thông tin task hiện tại
        $task = $this->taskModel->getTaskById($taskId);
        if (!$task)
            $this->jsonResponse(false, 'Task không tồn tại');

        // Lấy thông tin project của task
        $project = $this->projectModel->getProjectById($task['project_id']);

        if (!$project)
            $this->jsonResponse(false, 'Dự án của task không tồn tại');

        $startDate = $project['start_date'] ?? null;
        $endDate = $project['end_date'] ?? null;

        // Ràng buộc deadline phải nằm trong khoảng ngày của dự án
        if ($startDate && $endDate) {
            if ($deadline < $startDate || $deadline > $endDate) {
                $this->jsonResponse(false, "Hạn hoàn thành phải nằm trong khoảng từ $startDate đến $endDate của dự án.");
            }
        }

        $success = $this->taskModel->updateTask($taskId, $title, $description, $assigneeId, $deadline, $priority, $status);

        if ($success) {
            $this->jsonResponse(true, 'Cập nhật công việc thành công!');
        } else {
            $this->jsonResponse(false, 'Cập nhật thất bại!');
        }
    }

    // ✅ Xóa công việc
    public function deleteTask($taskId)
    {
        $result = $this->taskModel->deleteTask($taskId);
        echo json_encode([
            'success' => $result,
            'message' => $result ? 'Xóa công việc thành công!' : 'Xóa công việc thất bại!'
        ]);
    }


    // Hiển thị các task theo status
    public function indexTaskReceiver($user_id)
    {
        return [
            "pending" => $this->taskModel->getTasksByStatus("Pending", $user_id),
            "in_progress" => $this->taskModel->getTasksByStatus("In Progress", $user_id),
            "completed" => $this->taskModel->getTasksByStatus("Completed", $user_id),
            "overdue" => $this->taskModel->getTasksByStatus("Overdue", $user_id)
        ];
    }

    // Đổi trạng thái task
    public function changeStatus()
    {
        if (!isset($_POST['task_id']) || !isset($_POST['status'])) {
            die("INVALID REQUEST");
        }

        $task_id = $_POST['task_id'];
        $status = $_POST['status'];

        $this->taskModel->updateStatus($task_id, $status);
        // lấy project id của task
        $project_id = $this->taskModel->getProjectId($task_id);
        // cập nhật lại progress dự án
        $this->taskModel->updateProjectProgress($project_id);

        $progress = $this->taskModel->getProjectProgress($project_id);

        echo json_encode([
            "success" => true,
            "progress" => $progress
        ]);
    }

    public function sendReport()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $taskId = $_POST['task_id'] ?? null;
        $content = $_POST['report_content'] ?? null;
        $testMode = $_POST['test_mode'] ?? '0'; // Test mode flag

        if (!$taskId || !$content) {
            echo json_encode(['success' => false, 'message' => 'Vui lòng nhập đầy đủ thông tin báo cáo.']);
            return;
        }

        // Lấy thông tin task để dùng title tạo filename
        $task = $this->taskModel->getTaskById($taskId);
        if (!$task) {
            echo json_encode(['success' => false, 'message' => 'Task không tồn tại.']);
            return;
        }

        // Tạo slug từ title (bỏ dấu, bỏ ký tự đặc biệt)
        $baseName = $this->slugify($task['title']);

        /* -------------------
        XỬ LÝ FILE UPLOAD - Chọn controller theo mode
        --------------------*/
        $fileName = null;
        $fileOriginalName = null;

        if (isset($_FILES['report_file']) && $_FILES['report_file']['error'] === UPLOAD_ERR_OK) {
            $uploadCtrl = new cUpload();

            // TEST MODE: Không scan malware, accept tất cả file types
            if ($testMode === '1') {
                $uploadResult = $uploadCtrl->uploadWithoutScan('report_file', $baseName, 10 * 1024 * 1024);

                if ($uploadResult['status'] === 'success') {
                    // Log warning
                    error_log('[TEST MODE] Report uploaded without security scan for task_id: ' . $taskId);
                }
            }
            // PRODUCTION MODE: Full security scan
            else {
                $uploadResult = $uploadCtrl->uploadReport('report_file', $baseName, 5 * 1024 * 1024);
            }

            if ($uploadResult['status'] !== 'success') {
                echo json_encode(['success' => false, 'message' => $uploadResult['message']]);
                return;
            }

            $fileName = $uploadResult['name']; // Tên file đã băm
            $fileOriginalName = $_FILES['report_file']['name']; // Tên gốc
        }

        // Lưu xuống Database
        $result = $this->taskModel->saveReport($taskId, $content, $fileName, $fileOriginalName);

        if ($result) {
            $msg = 'Gửi báo cáo thành công!';
            if ($testMode === '1') {
                $msg .= ' [TEST MODE - No Security Scan]';
            }
            echo json_encode(['success' => true, 'message' => $msg]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Không thể gửi báo cáo.']);
        }
    }

    // Hàm lưu kết quả đánh giá task
    public function saveTaskReview($taskId, $result)
    {
        if (!$taskId || !$result) {
            return ['success' => false, 'message' => 'Thiếu dữ liệu bắt buộc'];
        }

        $success = $this->taskModel->updateTaskResult($taskId, $result);

        if ($success) {
            return ['success' => true, 'message' => 'Đánh giá task thành công'];
        } else {
            return ['success' => false, 'message' => 'Lỗi khi lưu đánh giá'];
        }
    }


    // ✅ Hàm trả JSON thống nhất
    private function jsonResponse($success, $message, $data = [])
    {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data
        ]);
        exit;
    }

    // Xóa file báo cáo
    public function deleteReportFile()
    {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }

        $taskId = $_POST['task_id'] ?? null;
        $fileName = $_POST['file_name'] ?? null;

        if (!$taskId || !$fileName) {
            echo json_encode(['success' => false, 'message' => 'Thiếu thông tin để xóa file.']);
            return;
        }

        // Kiểm tra task có tồn tại không
        $task = $this->taskModel->getTaskById($taskId);
        if (!$task) {
            echo json_encode(['success' => false, 'message' => 'Task không tồn tại.']);
            return;
        }

        // Kiểm tra quyền xóa (chỉ người được giao việc mới được xóa)
        if (isset($_SESSION['user_id']) && $task['assigned_to'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'Bạn không có quyền xóa file này.']);
            return;
        }

        // Xóa file trong database trước (để tránh hiển thị file đã xóa)
        $updated = $this->taskModel->removeReportFile($taskId);

        if (!$updated) {
            echo json_encode(['success' => false, 'message' => 'Không thể cập nhật database.']);
            return;
        }

        // Sau đó xóa file vật lý
        $uploadCtrl = new cUpload();
        $deleteResult = $uploadCtrl->deleteReport($fileName);

        // Dù file vật lý có xóa được hay không, database đã được cập nhật
        if ($deleteResult['success']) {
            echo json_encode(['success' => true, 'message' => 'Xóa file thành công!']);
        } else {
            // File vật lý không xóa được nhưng DB đã update
            echo json_encode([
                'success' => true,
                'message' => 'Đã xóa tham chiếu file. ' . $deleteResult['message']
            ]);
        }
    }

    private function slugify($str)
    {
        // Chuyển tiếng Việt có dấu → không dấu
        $str = mb_strtolower($str, 'UTF-8');

        $unicode = [
            'a' => 'áàạảãâấầậẩẫăắằặẳẵ',
            'e' => 'éèẹẻẽêếềệểễ',
            'i' => 'íìịỉĩ',
            'o' => 'óòọỏõôốồộổỗơớờợởỡ',
            'u' => 'úùụủũưứừựửữ',
            'y' => 'ýỳỵỷỹ',
            'd' => 'đ',
        ];

        foreach ($unicode as $nonUnicode => $uniChars) {
            $str = preg_replace("/[$uniChars]/u", $nonUnicode, $str);
        }

        // Xóa ký tự đặc biệt
        $str = preg_replace('/[^a-z0-9]+/i', '-', $str);

        // Xóa dấu - dư
        $str = trim($str, '-');

        return $str;
    }
}

// ===============================
// Xử lý request ngoài route MVC
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller = new TaskController();
    $action = $_POST['action'] ?? $_GET['action'] ?? null;

    switch ($action) {
        case 'addTask':
            $controller->addTask();
            break;
        case 'getTasks':
            $controller->index();
            break;
        case 'updateTask':
            $controller->updateTask();
            break;
        case 'getTaskById':
            $task = $controller->getTaskById($_GET['task_id'] ?? null);
            if ($task) {
                echo json_encode(['success' => true, 'data' => $task]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Công việc không tồn tại.']);
            }
            break;
        case 'getTasks_Id':
            $task = $controller->getTasks_ID($_GET['task_id']);
            if ($task) {
                echo json_encode(['success' => true, 'data' => $task]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Công việc không tồn tại.']);
            }
            break;
        case 'delete':
            $controller->deleteTask($_GET['task_id'] ?? null);
            break;
        case 'changeStatus':
            $controller->changeStatus();
            break;
        case 'sendReport':
            $controller->sendReport();
            break;
        case 'review_detail':
            $taskId = $_GET['task_id'] ?? 0;
            echo $controller->getTaskById($taskId);
            exit;
        case 'saveTaskReview':
            $taskId = $_POST['task_id'] ?? null;
            $result = $_POST['result'] ?? null;

            header('Content-Type: application/json; charset=utf-8');
            $res = $controller->saveTaskReview($taskId, $result);
            echo json_encode($res);
            exit;
        case 'getTasksByReceiver':
            $user_id = $_GET['user_id'] ?? null;
            header('Content-Type: application/json; charset=utf-8');
            $data = $controller->indexTaskReceiver($user_id);
            echo json_encode(['success' => true, 'data' => $data]);
            exit;
        case 'deleteReportFile':
            $controller->deleteReportFile();
            exit; // Thêm exit để không có output thêm
        case 'toggleDevMode':
            header('Content-Type: application/json; charset=utf-8');
            $enable = ($_POST['enable'] ?? 'false') === 'true';
            $uploadCtrl = new cUpload();

            if ($enable) {
                $result = $uploadCtrl->enableDevMode();
            } else {
                $result = $uploadCtrl->disableDevMode();
            }

            echo json_encode($result);
            exit;
        case 'checkDevMode':
            header('Content-Type: application/json; charset=utf-8');
            $uploadCtrl = new cUpload();
            $isEnabled = $uploadCtrl->isDevModeEnabled();
            echo json_encode(['success' => true, 'enabled' => $isEnabled]);
            exit;
        default:
            // echo json_encode(['success' => false, 'message' => 'Hành động không hợp lệ.']);
            break;
    }
}
