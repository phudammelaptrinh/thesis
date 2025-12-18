<?php
require_once __DIR__ . '/DatabaseModel.php';

class User extends mDatabase
{
    protected $table = "users";

    //  Lấy danh sách người dùng đang hoạt động (phục vụ giao việc)
    public function getActiveUsers()
    {
        $query = "SELECT user_id, full_name, email FROM {$this->table} WHERE status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    //  Kiểm tra vai trò (admin, manager, staff)
    public function getRole($user_id)
    {
        $stmt = $this->conn->prepare("
            SELECT r.role_name 
            FROM roles r 
            JOIN users u ON u.role_id = r.role_id 
            WHERE u.user_id = ?
        ");
        $stmt->execute([$user_id]);
        $role = $stmt->fetch(PDO::FETCH_ASSOC);
        return $role ? $role['role_name'] : null;
    }
    public function getUsersByRole($roleName)
    {
        $sql = "SELECT u.user_id, u.full_name, u.status 
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            WHERE r.role_name = :roleName 
              AND u.status = 'active'";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':roleName' => $roleName]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUsersReceivingTasks()
    {
        $sql = "SELECT DISTINCT u.user_id, u.full_name, u.email, u.status, r.role_name, t.assigned_to as assigned_to
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            JOIN tasks t ON t.assigned_to = u.user_id
            WHERE r.role_name = 'Nhận việc'";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getAllUsers($user_id)
    {
        $sql = "
            SELECT 
                u.user_id, 
                u.full_name, 
                u.email, 
                r.role_name AS role, 
                u.status,
                u.created_at, 
                u.updated_at
            FROM users u
            JOIN roles r ON u.role_id = r.role_id
            where u.user_id = :user_id
            ORDER BY u.user_id ASC
        ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllRoles()
    {
        $sql = "SELECT * FROM roles ORDER BY role_id ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":email", $email, PDO::PARAM_STR);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    public function getUsers_Role($user_id)
    {
        $sql = "SELECT u.*, r.role_name AS role_name
                FROM users u
                JOIN roles r ON u.role_id = r.role_id                
                WHERE u.user_id = :user_id
                ";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getUserById($user_id)
    {
        $sql = "SELECT * FROM users WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // fetch 1 bản ghi
    }
    public function getUserWithRole($user_id)
    {
        $sql = "SELECT u.*, r.role_name
            FROM users u
            LEFT JOIN roles r ON u.role_id = r.role_id
            WHERE u.user_id = :user_id
            LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([':user_id' => $user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC); // fetch 1 bản ghi
    }

    public function deleteUser($id)
    {
        $sql = "DELETE FROM users WHERE user_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":id", $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function searchUsers($keyword = '', $role_id = '')
    {
        $sql = "
        SELECT 
            u.user_id, 
            u.full_name, 
            u.email, 
            r.role_name AS role, 
            u.status, 
            u.created_at, 
            u.updated_at
        FROM users u
        LEFT JOIN roles r ON u.role_id = r.role_id
        WHERE 1=1
    ";

        $params = [];

        // 🔍 Tìm theo tên hoặc email
        if (!empty($keyword)) {
            $sql .= " AND (u.full_name LIKE :kw1 OR u.email LIKE :kw2) ";
            $params[':kw1'] = "%$keyword%";
            $params[':kw2'] = "%$keyword%";
        }

        // 🎭 Tìm theo role_id
        if (!empty($role_id) && $role_id !== '') {
            $sql .= " AND u.role_id = :role_id ";
            $params[':role_id'] = (int) $role_id;
        }

        $sql .= " ORDER BY u.user_id ASC";

        try {
            $stmt = $this->conn->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Search Users Error: " . $e->getMessage());
            error_log("SQL: " . $sql);
            error_log("Params: " . print_r($params, true));
            return [];
        }
    }
    public function addUser($name, $email, $password, $role_id, $status)
    {
        $sql = "INSERT INTO users (full_name, email, password, role_id, status, created_at, updated_at)
                VALUES (:name, :email, :password, :role_id, :status, NOW(), NOW())";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $name,
            ':email' => $email,
            ':password' => $password,  // không băm
            ':role_id' => $role_id,
            ':status' => $status
        ]);
    }
    // updateUser tương ứng (nếu chưa có)
    public function updateUser($id, $full_name, $email, $role_id, $status)
    {
        $sql = "UPDATE users 
                SET full_name = :name, email = :email, role_id = :role_id, status = :status, updated_at = NOW()
                WHERE user_id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':name' => $full_name,
            ':email' => $email,
            ':role_id' => $role_id,
            ':status' => $status,
            ':id' => $id
        ]);
    }

    public function updateProfile($user_id, $full_name, $email)
    {
        $sql = "UPDATE users SET full_name = :full_name, email = :email, updated_at = NOW() WHERE user_id = :uid";
        $stmt = $this->conn->prepare($sql);

        return $stmt->execute([
            'full_name' => $full_name,
            'email' => $email,
            'uid' => $user_id
        ]);
    }

    /**
     * Cập nhật mật khẩu người dùng
     * @param int $user_id - ID người dùng
     * @param string $newPassword - Mật khẩu mới
     * @return bool
     */
    public function changePassword($user_id, $newPassword)
    {
        $sql = "UPDATE users 
            SET password = :password, updated_at = NOW() 
            WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([
            ':password' => $newPassword,
            ':user_id' => $user_id
        ]);
    }
}