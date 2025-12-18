<?php
// config/database.php
// ⚠️ File này chứa thông tin nhạy cảm - KHÔNG commit lên Git!
// Sử dụng .env hoặc config riêng cho từng môi trường

class Database
{
    private $host;
    private $port;
    private $db_name;
    private $username;
    private $password;
    public $conn;

    public function __construct()
    {
        // Load .env file nếu tồn tại
        $this->loadEnv();

        // Load từ environment variables hoặc file .env nếu có
        $this->host = getenv('DB_HOST') ?: "127.0.0.1";
        $this->port = getenv('DB_PORT') ?: "3309";
        $this->db_name = getenv('DB_NAME') ?: "taskbb1";
        $this->username = getenv('DB_USER') ?: "root";
        $this->password = getenv('DB_PASS') ?: "";
    }

    private function loadEnv()
    {
        $envFile = __DIR__ . '/../.env';
        if (file_exists($envFile)) {
            $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                // Bỏ qua comment
                if (strpos(trim($line), '#') === 0) {
                    continue;
                }

                // Parse key=value
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $key = trim($key);
                    $value = trim($value);

                    // Bỏ quotes nếu có
                    $value = trim($value, '"\'');

                    // Set environment variable
                    if (!getenv($key)) {
                        putenv("$key=$value");
                    }
                }
            }
        }
    }

    public function connect()
    {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4",
                    PDO::ATTR_TIMEOUT => 5
                ]
            );
        } catch (PDOException $e) {
            // ⚠️ Production: không hiển thị chi tiết lỗi
            if (getenv('APP_ENV') === 'production') {
                error_log("Database connection failed: " . $e->getMessage());
                die("Lỗi kết nối cơ sở dữ liệu. Vui lòng thử lại sau.");
            } else {
                die("Kết nối cơ sở dữ liệu thất bại: " . $e->getMessage());
            }
        }

        return $this->conn;
    }
}
