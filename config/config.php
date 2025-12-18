<?php
// config/config.php - Cấu hình chung cho ứng dụng

// Load .env file
loadEnvFile();

function loadEnvFile()
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

// Tự động phát hiện base URL
function getBaseUrl()
{
    // Ưu tiên lấy từ .env
    $envUrl = getenv('APP_URL');
    if ($envUrl && $envUrl !== '') {
        return rtrim($envUrl, '/');
    }

    // Tự động phát hiện nếu không có trong .env
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];

    // Lấy đường dẫn thư mục chứa project
    $scriptName = $_SERVER['SCRIPT_NAME'];

    // Tìm base directory bằng cách loại bỏ phần sau /public hoặc /apps
    $baseDir = '';
    if (preg_match('#^(.*?)/(?:public|apps|index\.php)#', $scriptName, $matches)) {
        $baseDir = $matches[1];
    } elseif (dirname($scriptName) !== '/' && dirname($scriptName) !== '\\') {
        // Fallback: lấy thư mục chứa file
        $parts = explode('/', trim($scriptName, '/'));
        if (count($parts) > 1) {
            $baseDir = '/' . $parts[0];
        }
    }

    return $protocol . $host . $baseDir;
}

// Định nghĩa các hằng số toàn cục
define('BASE_URL', getBaseUrl());
define('BASE_PATH', dirname(__DIR__));

// Đường dẫn tài nguyên
define('CSS_URL', BASE_URL . '/public/css');
define('JS_URL', BASE_URL . '/public/js');
define('UPLOAD_URL', BASE_URL . '/public/uploads');
define('VIEWS_UPLOAD_URL', BASE_URL . '/Views/uploads');

// Đường dẫn file hệ thống
define('CONTROLLER_PATH', BASE_PATH . '/apps/Controllers');
define('MODEL_PATH', BASE_PATH . '/apps/Models');
define('VIEW_PATH', BASE_PATH . '/apps/Views');

// Môi trường
define('APP_ENV', getenv('APP_ENV') ?: 'development');
define('APP_URL', getenv('APP_URL') ?: BASE_URL);
define('DEBUG_MODE', getenv('APP_DEBUG') === 'true' || APP_ENV === 'development');

// Timezone
$timezone = getenv('APP_TIMEZONE') ?: 'Asia/Ho_Chi_Minh';
date_default_timezone_set($timezone);

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Helper function để tạo URL
function url($path = '')
{
    return BASE_URL . ($path ? '/' . ltrim($path, '/') : '');
}

// Helper function để tạo asset URL
function asset($path)
{
    return BASE_URL . '/public/' . ltrim($path, '/');
}
