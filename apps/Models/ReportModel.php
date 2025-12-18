<?php
require_once __DIR__ . '/DatabaseModel.php';

class ReportModel extends mDatabase
{
    /**
     * Lấy thống kê tổng quan cho trang giám sát & xuất báo cáo.
     * Bao gồm số lượng người dùng, dự án, công việc, tiến độ trung bình,...
     */
    public function getDashboardSummary()
    {
        $summary = [];

        // ==================== TOTALS ====================

        // Tổng số người dùng
        $sql = "SELECT COUNT(*) AS total_users FROM users";
        $summary['total_users'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_users'];

        // Tổng số người giao việc
        $sql = "SELECT COUNT(DISTINCT u.user_id) AS total_assigners
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            WHERE r.role_name = 'Giao việc'";
        $summary['total_assigners'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_assigners'];

        // Tổng số người nhận việc
        $sql = "SELECT COUNT(DISTINCT u.user_id) AS total_receivers
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            WHERE r.role_name = 'Nhận việc'";
        $summary['total_receivers'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_receivers'];

        // Tổng số dự án
        $sql = "SELECT COUNT(*) AS total_projects FROM projects";
        $summary['total_projects'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_projects'];

        // Tổng số task
        $sql = "SELECT COUNT(*) AS total_tasks FROM tasks";
        $summary['total_tasks'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_tasks'];

        // ==================== COMPLETED ====================

        // Tổng dự án hoàn thành
        $sql = "SELECT COUNT(*) AS total_projects_completed FROM projects WHERE status = 'Completed'";
        $summary['total_projects_completed'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_projects_completed'];

        // Tổng task hoàn thành
        $sql = "SELECT COUNT(*) AS total_tasks_completed FROM tasks WHERE status = 'Completed'";
        $summary['total_tasks_completed'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_tasks_completed'];

        // ==================== IN PROGRESS ====================

        // Tổng dự án đang thực hiện
        $sql = "SELECT COUNT(*) AS total_projects_processing 
            FROM projects 
            WHERE status IN ('Planning')";
        $summary['total_projects_processing'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_projects_processing'];

        // Tổng task đang làm
        $sql = "SELECT COUNT(*) AS total_tasks_doing 
            FROM tasks 
            WHERE status = 'In Progress'";
        $summary['total_tasks_doing'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_tasks_doing'];

        // ==================== NOT STARTED ====================

        // Tổng task chưa làm
        $sql = "SELECT COUNT(*) AS total_tasks_todo 
            FROM tasks 
            WHERE status = 'Pending'";
        $summary['total_tasks_todo'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_tasks_todo'];

        // ==================== OVERDUE ====================

        // Tổng task trễ hạn
        $sql = "SELECT COUNT(*) AS total_tasks_late 
            FROM tasks 
            WHERE status = 'Overdue'";
        $summary['total_tasks_late'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['total_tasks_late'];

        // ==================== CHART ====================

        // Số lượng task theo trạng thái
        $sql = "
        SELECT status, COUNT(*) AS count 
        FROM tasks 
        GROUP BY status
    ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $summary['tasks_by_status'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Trung bình tiến độ dự án
        $sql = "SELECT ROUND(AVG(progress), 2) AS avg_progress FROM projects";
        $summary['avg_progress'] = $this->conn->query($sql)->fetch(PDO::FETCH_ASSOC)['avg_progress'] ?? 0;

        return $summary;
    }

    public function getSummary_assigner($user_id)
    {
        $summary = [];

        // TOTAL PROJECTS
        $sql = "SELECT COUNT(*) AS total_projects FROM projects WHERE assigned_to = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['total_projects'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_projects'];

        // TOTAL COMPLETED PROJECTS
        $sql = "SELECT COUNT(*) AS total_projects_completed FROM projects WHERE assigned_to = :user_id AND status = 'Completed'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['total_projects_completed'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_projects_completed'];
        // TOTAL PLANNING PROJECT
        $sql = "SELECT COUNT(*) AS total_projects_planning FROM projects WHERE assigned_to = :user_id AND status = 'Planning'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['total_projects_planning'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_projects_planning'];
        // AVG PROGRESS
        $sql = "SELECT ROUND(AVG(progress), 2) AS avg_progress FROM projects WHERE assigned_to = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['avg_progress'] = $stmt->fetch(PDO::FETCH_ASSOC)['avg_progress'] ?? 0;

        // TOTAL TASKS
        $sql = "SELECT COUNT(*) AS total_tasks FROM tasks WHERE created_by = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['total_tasks'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_tasks'];

        return $summary;
    }

    public function getSummary_receiver($user_id)
    {
        $summary = [];
        // TOTAL TASKS
        $sql = "SELECT COUNT(*) AS total_tasks FROM tasks WHERE assigned_to = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['total_tasks'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_tasks'];
        // Total completed tasks
        $sql = "SELECT COUNT(*) AS total_tasks_completed FROM tasks WHERE assigned_to = :user_id AND status = 'Completed'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['total_tasks_completed'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_tasks_completed'];
        // Total overdue tasks
        $sql = "SELECT COUNT(*) AS total_tasks_late FROM tasks WHERE assigned_to = :user_id AND deadline < NOW() AND status = 'Overdue'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['total_tasks_late'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_tasks_late'];
        // Total in-progress tasks
        $sql = "SELECT COUNT(*) AS total_tasks_doing FROM tasks WHERE assigned_to = :user_id AND status = 'In Progress'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['total_tasks_doing'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_tasks_doing'];
        // Total pending tasks
        $sql = "SELECT COUNT(*) AS total_tasks_todo FROM tasks WHERE assigned_to = :user_id AND status = 'Pending'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $user_id);
        $stmt->execute();
        $summary['total_tasks_todo'] = $stmt->fetch(PDO::FETCH_ASSOC)['total_tasks_todo'];

        return $summary;
    }
    // ===================== LIST: ASSIGNERS =====================
    public function getAssigners()
    {
        $sql = "
            SELECT DISTINCT u.user_id, u.full_name, u.email, status
            FROM users u
            join roles r on u.role_id = r.role_id
            where r.role_name = 'Giao việc'
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ===================== LIST: RECEIVERS =====================
    public function getReceivers()
    {
        $sql = "
            SELECT DISTINCT u.user_id, u.full_name, u.email, status
            FROM users u
            join roles r on u.role_id = r.role_id
            where r.role_name = 'Nhận việc'
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách chi tiết dự án + số task và tiến độ trung bình
     */
    public function getProjectReports()
    {
        $sql = "
            SELECT 
                p.project_id,
                p.project_name,
                p.start_date,
                p.end_date,
                u.full_name AS created_by,
                COUNT(t.task_id) AS total_tasks,
                ROUND(AVG(a.progress), 2) AS avg_progress
            FROM projects p
            LEFT JOIN users u ON p.created_by = u.user_id
            LEFT JOIN tasks t ON p.project_id = t.project_id
            LEFT JOIN task_assignees a ON t.task_id = a.task_id
            GROUP BY p.project_id, p.project_name, u.full_name, p.start_date, p.end_date
            ORDER BY p.project_id ASC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy danh sách người dùng kèm vai trò và số lượng công việc được giao
     */
    public function getUserReports()
    {
        $sql = "
            SELECT 
                u.user_id,
                u.full_name,
                r.role_name,
                u.status,
                COUNT(a.task_id) AS total_assigned_tasks
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.role_id
            LEFT JOIN task_assignees a ON u.user_id = a.user_id
            GROUP BY u.user_id, u.full_name, r.role_name, u.status
            ORDER BY u.user_id ASC
        ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Lấy thống kê công việc theo project_id
    public function getStatsByProject($projectId)
    {
        // Lấy Progress của project
        $sqlProgress = "SELECT progress FROM projects WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($sqlProgress);
        $stmt->execute([':project_id' => $projectId]);
        $progress = $stmt->fetch(PDO::FETCH_ASSOC)['progress'] ?? 0;
        // Tổng công việc
        $sqlTotal = "SELECT COUNT(*) AS total FROM tasks WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($sqlTotal);
        $stmt->execute([':project_id' => $projectId]);
        $total = (int)$stmt->fetchColumn();

        // Theo trạng thái
        $sqlByStatus = "SELECT status, COUNT(*) AS cnt
                    FROM tasks
                    WHERE project_id = :project_id
                    GROUP BY status";
        $stmt = $this->conn->prepare($sqlByStatus);
        $stmt->execute([':project_id' => $projectId]);
        $statusRows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $byStatus = [];
        foreach ($statusRows as $r) {
            $byStatus[$r['status']] = (int)$r['cnt'];
        }

        // Overdue: deadline < today AND status != completed (tùy tên status)
        $sqlOverdue = "SELECT COUNT(*) FROM tasks 
                   WHERE project_id = :project_id 
                     AND deadline < CURDATE() 
                     AND status NOT IN ('completed','Completed','Completed')";
        $stmt = $this->conn->prepare($sqlOverdue);
        $stmt->execute([':project_id' => $projectId]);
        $overdue = (int)$stmt->fetchColumn();

        // Assigned count (tasks that have an assigned_to not null)
        $sqlAssigned = "SELECT COUNT(*) FROM tasks WHERE project_id = :project_id AND (assigned_to IS NOT NULL AND assigned_to <> '')";
        $stmt = $this->conn->prepare($sqlAssigned);
        $stmt->execute([':project_id' => $projectId]);
        $assigned = (int)$stmt->fetchColumn();

        // Dữ liệu thời gian (tasks created per day trong 30 ngày gần nhất) — dùng cho biểu đồ
        $sqlTimeseries = "SELECT DATE(created_at) as dt, COUNT(*) as cnt
                      FROM tasks
                      WHERE project_id = :project_id
                        AND created_at >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
                      GROUP BY DATE(created_at)
                      ORDER BY DATE(created_at) ASC";
        $stmt = $this->conn->prepare($sqlTimeseries);
        $stmt->execute([':project_id' => $projectId]);
        $timeseries = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'total' => $total,
            'by_status' => $byStatus,
            'overdue' => $overdue,
            'assigned' => $assigned,
            'timeseries' => $timeseries,
            'progress' => $progress
        ];
    }
    public function getProjects_ID($userId)
    {
        $sql = "
        SELECT p.project_id, p.project_name
        FROM projects p
        WHERE p.assigned_to = :user_id
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTasksByProject($projectId)
    {
        $sql = "SELECT t.*, u.full_name AS assignee
            FROM tasks t
            LEFT JOIN users u ON u.user_id = t.assigned_to
            WHERE t.project_id = :pid
            ORDER BY t.status";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['pid' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}