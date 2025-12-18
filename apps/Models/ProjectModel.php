<?php
require_once __DIR__ . '/DatabaseModel.php';

class Project extends mDatabase
{
    // Lấy tất cả dự án
    public function getAllProjects()
    {
        $sql = "SELECT p.*, u.full_name AS assigned_to_name
                FROM projects p
                LEFT JOIN users u ON p.assigned_to = u.user_id
                ORDER BY p.project_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Thêm dự án mới
    public function addProject($project_name, $description, $assigned_to, $start_date, $end_date, $progress, $status)
    {
        $sql = "INSERT INTO projects (project_name, description, created_by, assigned_to, start_date, end_date, progress, status)
                VALUES (:project_name, :description, 1, :assigned_to, :start_date, :end_date, :progress, :status)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':project_name' => $project_name,
            ':description' => $description,
            ':assigned_to' => $assigned_to,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':progress' => $progress,
            ':status' => $status
        ]);
        return $this->conn->lastInsertId();
    }

    // Lấy thông tin 1 dự án
    public function getProjectById($project_id)
    {
        $sql = "SELECT p.*, u.full_name AS created_by_name
                FROM projects p
                LEFT JOIN users u ON p.created_by = u.user_id
                WHERE p.project_id = :project_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':project_id', $project_id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật dự án
    public function update_Project($project_id, $project_name, $description, $assigned_to, $start_date, $end_date, $progress, $status)
    {
        $sql = "UPDATE projects
                SET project_name = :project_name,
                    description = :description,
                    assigned_to = :assigned_to,
                    start_date = :start_date,
                    end_date = :end_date,
                    progress = :progress,
                    status = :status
                WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([
            ':project_name' => $project_name,
            ':description' => $description,
            ':assigned_to' => $assigned_to,
            ':start_date' => $start_date,
            ':end_date' => $end_date,
            ':progress' => $progress,
            ':status' => $status,
            ':project_id' => $project_id
        ]);
        return $stmt->rowCount() > 0;
    }

    // Xóa dự án
    public function deleteProject($project_id)
    {
        $sql = "DELETE FROM projects WHERE project_id = :project_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':project_id', $project_id);
        return $stmt->execute();
    }

    // Tìm kiếm dự án theo tên hoặc người tạo
    public function searchProjects($keyword = '', $status = '')
    {
        $sql = "SELECT p.*, u.full_name AS assigned_to_name
            FROM projects p
            LEFT JOIN users u ON p.assigned_to = u.user_id
            WHERE 1=1"; // Luôn đúng để dễ nối điều kiện

        $params = [];

        if (!empty($keyword)) {
            $sql .= " AND (p.project_name LIKE :kw1 OR p.description LIKE :kw2 OR u.full_name LIKE :kw3)";
            $params[':kw1'] = "%$keyword%";
            $params[':kw2'] = "%$keyword%";
            $params[':kw3'] = "%$keyword%";
        }

        if (!empty($status) && $status !== '') {
            $sql .= " AND p.status = :status";
            $params[':status'] = $status;
        }

        $sql .= " ORDER BY p.project_id ASC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Search Projects Error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . print_r($params, true));
            return [];
        }
    }

    public function assignProjectToUser($projectId, $userId)
    {
        $query = "UPDATE projects SET assigned_to = :userId WHERE project_id = :projectId";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':projectId', $projectId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function unassignProject($project_id)
    {
        $sql = "UPDATE projects SET assigned_to = NULL WHERE project_id = :pid";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute(['pid' => $project_id]);
    }
    public function getProjectsByUser($userId)
    {
        $stmt = $this->conn->prepare("SELECT * FROM projects WHERE assigned_to = :user_id");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    public function getMembersByProjectId($projectId)
    {
        $stmt = $this->conn->prepare("SELECT u.user_id, u.full_name FROM project_members pm 
                                  JOIN users u ON pm.user_id = u.user_id 
                                  WHERE pm.project_id = :id");
        $stmt->execute([':id' => $projectId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
