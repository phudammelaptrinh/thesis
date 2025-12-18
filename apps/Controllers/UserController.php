    <?php
    require_once __DIR__ . '/../Models/UserModel.php';
    // session_start();

    class UserController
    {
        private $userModel;

        public function __construct()
        {
            $this->userModel = new User();
        }

        public function index($user_id)
        {
            return $this->userModel->getAllUsers($user_id);
        }
        public function getAllRoles()
        {
            return $this->userModel->getAllRoles();
        }
        public function getUsers_Role($user_id)
        {
            return $this->userModel->getUsers_Role($user_id);
        }
        public function getUserWithRole($user_id)
        {
            return $this->userModel->getUserWithRole($user_id);
        }
        public function getRoleUsers($roleName)
        {
            return $this->userModel->getUsersByRole($roleName);
        }
        public function getUserRole_ReceiverTasks()
        {
            return $this->userModel->getUsersReceivingTasks();
        }
        public function getUserById($id)
        {
            return $this->userModel->getUserById($id);
        }

        public function searchUsers($keyword, $role_id)
        {
            return $this->userModel->searchUsers($keyword, $role_id);
        }

        public function addUser()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = $_POST['name'] ?? '';
                $email = $_POST['email'] ?? '';
                $role_id = $_POST['role_id'] ?? '';
                $status = $_POST['status'] ?? 'inactive';
                $password = '123456'; // mặc định
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $success = $this->userModel->addUser($name, $email, $hashedPassword, $role_id, $status);

                if ($success) {
                    $_SESSION['success'] = "Thêm người dùng thành công!";
                } else {
                    $_SESSION['error'] = "Không thể thêm người dùng!";
                }

                header("Location: ../Views/admin/index.php?page=users");
                exit();
            }
        }

        public function update_user()
        {
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'update') {
                $id = $_POST['id'];
                $name = $_POST['name'];
                $email = $_POST['email'];
                $role_id = $_POST['role_id'];
                $status = $_POST['status'];

                $success = $this->userModel->updateUser($id, $name, $email, $role_id, $status);
                if ($success) {
                    $_SESSION['success'] = "Cập nhật người dùng thành công!";
                } else {
                    $_SESSION['error'] = "Cập nhật thất bại!";
                }

                header("Location: ../Views/admin/index.php?page=users");
                exit;
            }
        }

        public function deleteUser($id)
        {
            $success = $this->userModel->deleteUser($id);
            if ($success) {
                $_SESSION['success'] = "Xóa người dùng thành công!";
            } else {
                $_SESSION['error'] = "Xóa người dùng thất bại!";
            }
        }

        public function updateProfile()
        {
            header('Content-Type: application/json');
            $user_id = $_POST['user_id'] ?? null;
            $full_name = $_POST['full_name'] ?? '';
            $email = $_POST['email'] ?? '';

            if (!$user_id) {
                echo json_encode(['success' => false, 'message' => 'ID người dùng không hợp lệ.']);
                return;
            }

            $success = $this->userModel->updateProfile($user_id, $full_name, $email);

            if ($success) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật hồ sơ thành công!']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Cập nhật hồ sơ thất bại!']);
            }
            exit;
        }
    }


    // Gọi khi form submit
    if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'GET') {
        $controller = new UserController();
        $action = $_GET['action'] ?? $_POST['action'] ?? null;

        if ($action) {
            switch ($action) {
                case 'add':
                    $controller->addUser();
                    break;

                case 'update':
                    $controller->update_user();
                    break;

                case 'updateProfile':
                    $controller->updateProfile();
                    break;
            }
        }
    }