<?php
require_once __DIR__ . '/DatabaseModel.php';

class Task extends mDatabase
{
    // ✅ Lấy tất cả công việc theo project_id
    public function getTasksByProjectId($projectId)
    {
        $sql = "SELECT t.*, u.full_name AS assignee_name
                FROM tasks t
                LEFT JOIN users u ON t.assigned_to = u.user_id
                WHERE t.project_id = :project_id
                ORDER BY t.created_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Lấy thông tin một công việc theo id
    public function getTaskById($taskId)
    {
        $sql = "SELECT * FROM tasks WHERE task_id = :task_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':task_id' => $taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTasks_ID($taskId)
    {
        $sql = "SELECT t.*, u.full_name AS assignee_name
            FROM tasks t
            LEFT JOIN users u ON t.assigned_to = u.user_id
            WHERE t.task_id = :taskId";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['taskId' => $taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTasks_ProjectId($userId)
    {
        $sql = "SELECT t.*, p.project_name as project_name, p.progress as progress
                FROM tasks t
                LEFT JOIN projects p ON t.project_id = p.project_id
                WHERE t.assigned_to = :userId
                ORDER BY t.created_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTasks_Assigner($userId)
    {
        $sql = "SELECT t.*, p.project_name as project_name, u.full_name AS assignee_name, p.progress as progress
                FROM tasks t
                JOIN projects p ON t.project_id = p.project_id
                JOIN users u ON t.assigned_to = u.user_id
                WHERE t.created_by = :userId
                ORDER BY t.created_at ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':userId' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTasksByUser($userId)
    {
        $sql = "SELECT * FROM tasks t
        WHERE assigned_to = :user_id 
        ORDER BY deadline DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // ✅ Thêm công việc mới
    public function addTask($title, $description, $project_id, $created_by, $assigned_to, $deadline, $priority)
    {
        $sql = "INSERT INTO tasks 
                (title, description, project_id, created_by, assigned_to, deadline, priority, status, created_at, updated_at)
                VALUES (:title, :description, :project_id, :created_by, :assigned_to, :deadline, :priority, 'Pending', NOW(), NOW())";

        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':title' => $title,
            ':description' => $description,
            ':project_id' => $project_id,
            ':created_by' => $created_by,
            ':assigned_to' => $assigned_to,
            ':deadline' => $deadline,
            ':priority' => $priority
        ]);
    }

    // ✅ Cập nhật công việc
    public function updateTask($task_id, $title, $description, $assigned_to, $deadline, $priority, $status)
    {
        $sql = "UPDATE tasks 
                SET title = :title, description = :description, 
                    assigned_to = :assigned_to, deadline = :deadline, priority = :priority, status = :status 
                WHERE task_id = :task_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':task_id' => $task_id,
            ':title' => $title,
            ':description' => $description,
            ':assigned_to' => $assigned_to,
            ':deadline' => $deadline,
            ':priority' => $priority,
            ':status' => $status
        ]);
    }

    // ✅ Xóa công việc
    public function deleteTask($taskId)
    {
        $sql = "DELETE FROM tasks WHERE task_id = :task_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':task_id' => $taskId]);
    }

    // Lấy task theo status và assigned_to
    public function getTasksByStatus($status, $user_id)
    {
        $sql = "SELECT tasks.*, projects.project_name AS project_name, users.full_name AS full_name, projects.progress AS progress
                FROM tasks
                JOIN projects ON tasks.project_id = projects.project_id
                JOIN users ON tasks.created_by = users.user_id
                WHERE tasks.status = ? AND tasks.assigned_to = ?
                ORDER BY tasks.priority DESC, tasks.deadline ASC";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$status, $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTasks_Receiver($receiverId)
    {
        $sql = " SELECT t.*,p.project_name AS project_name,ua.full_name AS assigner_name
        FROM tasks t
        LEFT JOIN projects p ON p.project_id = t.project_id
        LEFT JOIN users ua ON ua.user_id = t.created_by
        WHERE t.assigned_to = :receiver
        ORDER BY p.project_id, t.task_id DESC";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':receiver', $receiverId);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái task
    public function updateStatus($task_id, $status)
    {
        $sql = "UPDATE tasks SET status = ?, updated_at = NOW() WHERE task_id = ?";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([$status, $task_id]);
    }

    public function saveReport($taskId, $content, $fileName = null, $fileOriginalName = null)
    {
        try {
            $sql = "UPDATE tasks 
                SET report_content = :content,
                    report_file = :file,
                    report_file_original = :file_original,
                    updated_at = NOW()
                WHERE task_id = :task_id";

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':content', $content);
            $stmt->bindParam(':file', $fileName);
            $stmt->bindParam(':file_original', $fileOriginalName);
            $stmt->bindParam(':task_id', $taskId);

            return $stmt->execute();
        } catch (PDOException $e) {
            return false;
        }
    }

    public function getCompletedTasksByUser($user_id)
    {
        $sql = "SELECT t.*, p.project_name, u.full_name as assignee_name, p.progress as progress 
            FROM tasks t
            JOIN projects p ON t.project_id = p.project_id
            JOIN users u ON t.assigned_to = u.user_id
            WHERE t.status = 'Completed' AND t.created_by = :user_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTask_detail($taskId)
    {
        $stmt = $this->conn->prepare("SELECT t.*, p.project_name 
                                FROM tasks t
                                JOIN projects p ON t.project_id = p.project_id
                                WHERE t.task_id = ?");
        $stmt->execute([$taskId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateTaskResult($taskId, $result)
    {
        $sql = "UPDATE tasks SET result = :result, updated_at = NOW() 
        WHERE task_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            'result' => $result,
            'id' => $taskId
        ]);
    }

    public function updateProjectProgress($project_id)
    {
        // tổng task
        $sql = "SELECT COUNT(*) FROM tasks WHERE project_id = :pid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['pid' => $project_id]);
        $total = $stmt->fetchColumn();

        if ($total == 0) {
            $progress = 0;
        } else {
            // task completed
            $sql = "SELECT COUNT(*) FROM tasks WHERE project_id = :pid AND status = 'Completed'";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute(['pid' => $project_id]);
            $completed = $stmt->fetchColumn();

            $progress = round(($completed / $total) * 100);
        }

        // update progress
        $sql = "UPDATE projects SET progress = :p WHERE project_id = :pid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['p' => $progress, 'pid' => $project_id]);
    }

    public function getProjectId($task_id)
    {
        $sql = "SELECT project_id FROM tasks WHERE task_id = :tid";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute(['tid' => $task_id]);
        return $stmt->fetchColumn();
    }

    public function getProjectProgress($project_id)
    {
        $stmt = $this->conn->prepare("SELECT progress FROM projects WHERE project_id = :pid");
        $stmt->execute(['pid' => $project_id]);
        return $stmt->fetchColumn();
    }

    // Xóa file báo cáo khỏi task (set report_file = NULL)
    public function removeReportFile($taskId)
    {
        $sql = "UPDATE tasks SET report_file = NULL, report_file_original = NULL WHERE task_id = :task_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':task_id' => $taskId]);
    }
}
