<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Models/ReportModel.php';
require_once __DIR__ . '/../Models/ProjectModel.php';

class ReportController
{
    private $reportModel;
    private $projectModel;


    public function __construct()
    {
        $this->reportModel = new ReportModel();
        $this->projectModel = new Project();
    }

    public function index()
    {
        // Lấy dữ liệu thống kê từ Model
        $summary = $this->reportModel->getDashboardSummary();

        // Chuẩn hóa dữ liệu
        $stats = [
            'total_users'                  => (int)($summary['total_users'] ?? 0),
            'total_assigners'              => (int)($summary['total_assigners'] ?? 0),
            'total_receivers'              => (int)($summary['total_receivers'] ?? 0),
            'total_projects'               => (int)($summary['total_projects'] ?? 0),
            'total_tasks'                  => (int)($summary['total_tasks'] ?? 0),
            'total_projects_completed'     => (int)($summary['total_projects_completed'] ?? 0),
            'total_tasks_completed'        => (int)($summary['total_tasks_completed'] ?? 0),
            'total_projects_processing'    => (int)($summary['total_projects_processing'] ?? 0),
            'total_tasks_doing'            => (int)($summary['total_tasks_doing'] ?? 0),
            'total_tasks_todo'             => (int)($summary['total_tasks_todo'] ?? 0),
            'total_tasks_late'             => (int)($summary['total_tasks_late'] ?? 0),
            'completed_rate'               => (float)($summary['avg_progress'] ?? 0),
            'In Progress'                  => 0,
            'Completed'                    => 0,
            'Overdue'                      => 0
        ];

        // Đếm task theo trạng thái (chuẩn hóa cho biểu đồ)
        if (!empty($summary['tasks_by_status'])) {
            foreach ($summary['tasks_by_status'] as $row) {
                $status = strtolower(trim($row['status']));
                $count = (int)$row['count'];

                if (in_array($status, ['doing', 'in progress', 'đang làm'])) {
                    $stats['In Progress'] = $count;
                } elseif (in_array($status, ['done', 'completed', 'hoàn thành'])) {
                    $stats['Completed'] = $count;
                } elseif (in_array($status, ['overdue', 'trễ hạn', 'late'])) {
                    $stats['Overdue'] = $count;
                }
            }
        }

        // Lấy danh sách người giao và người nhận
        $assigners = $this->reportModel->getAssigners();
        $receivers = $this->reportModel->getReceivers();

        // Trả dữ liệu cho view
        return [
            'stats'     => $stats,
            'assigners' => $assigners,
            'receivers' => $receivers
        ];
    }

    public function getSummary_assigner($user_id)
    {
        // Lấy dữ liệu thống kê từ Model
        $summary_assigner = $this->reportModel->getSummary_assigner($user_id);

        // Chuẩn hóa dữ liệu
        $stats = [
            'total_projects'               => (int)($summary_assigner['total_projects'] ?? 0),
            'total_projects_completed'     => (int)($summary_assigner['total_projects_completed'] ?? 0),
            'total_projects_planning'    => (int)($summary_assigner['total_projects_planning'] ?? 0),
            'avg_progress'                 => (float)($summary_assigner['avg_progress'] ?? 0),
            'total_tasks'                  => (int)($summary_assigner['total_tasks'] ?? 0)
        ];

        // Trả dữ liệu cho view
        return [
            'stats'     => $stats
        ];
    }

    public function getSummary_receiver($user_id)
    {
        // Lấy dữ liệu thống kê từ Model
        $summary_receiver = $this->reportModel->getSummary_receiver($user_id);

        // Chuẩn hóa dữ liệu
        $stats = [
            'total'     => (int)($summary_receiver['total_tasks'] ?? 0),
            'todo'      => (int)($summary_receiver['total_tasks_todo'] ?? 0),
            'doing'     => (int)($summary_receiver['total_tasks_doing'] ?? 0),
            'completed' => (int)($summary_receiver['total_tasks_completed'] ?? 0),
            'overdue'   => (int)($summary_receiver['total_tasks_late'] ?? 0)
        ];

        // Trả dữ liệu cho view
        return [
            'stats'     => $stats
        ];
    }

    // Trả danh sách project (id + name) cho select
    public function listProjects()
    {
        header('Content-Type: application/json');

        // Lấy user_id hiện tại từ session
        $user_id = $_SESSION['user_id'] ?? null;
        if (!$user_id) {
            echo json_encode(['success' => false, 'message' => 'Chưa đăng nhập']);
            exit;
        }

        // Lấy các project mà user này đảm nhận
        $projects = $this->reportModel->getProjects_ID($user_id);

        // map chỉ lấy id + name
        $out = [];
        foreach ($projects as $p) {
            $out[] = [
                'project_id' => $p['project_id'],
                'project_name' => $p['project_name']
            ];
        }

        echo json_encode(['success' => true, 'data' => $out]);
        exit;
    }

    // Trả thống kê cho 1 project_id
    public function projectStats()
    {
        header('Content-Type: application/json');
        $projectId = $_GET['project_id'] ?? $_POST['project_id'] ?? null;
        if (!$projectId) {
            echo json_encode(['success' => false, 'message' => 'Thiếu project_id']);
            exit;
        }

        // Kiểm tra project tồn tại
        $project = $this->projectModel->getProjectById($projectId);
        if (!$project) {
            echo json_encode(['success' => false, 'message' => 'Không tìm thấy dự án']);
            exit;
        }

        $stats = $this->reportModel->getStatsByProject($projectId);

        // Chuẩn hóa một số key cho UI
        $payload = [
            'project' => [
                'project_id' => $project['project_id'],
                'project_name' => $project['project_name'],
                'start_date' => $project['start_date'] ?? null,
                'end_date' => $project['end_date'] ?? null
            ],
            'stats' => [
                'total_tasks' => $stats['total'],
                'assigned' => $stats['assigned'],
                'overdue' => $stats['overdue'],
                'by_status' => $stats['by_status'],
                'timeseries' => $stats['timeseries'],
                'progress' => $stats['progress'] ?? 0
            ]
        ];

        echo json_encode(['success' => true, 'data' => $payload]);
        exit;
    }
}
// Router đơn giản
$controller = new ReportController();
$action = $_GET['action'] ?? $_POST['action'] ?? null;

if ($action === 'listProjects') {
    $controller->listProjects();
} elseif ($action === 'projectStats') {
    $controller->projectStats();
} elseif ($action === 'projectTasks') {
    $projectId = $_GET['project_id'] ?? 0;
    $report = new ReportModel();
    $tasks = $report->getTasksByProject($projectId);
    echo json_encode(['success' => true, 'data' => $tasks]);
}