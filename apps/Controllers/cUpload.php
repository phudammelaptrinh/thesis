<?php
// apps/Controllers/cUpload.php

class cUpload
{
    private string $uploadDir;
    private string $uploadWebPath;
    private array $allowedMimes;
    private array $extMap;

    public function __construct()
    {
        // Thiết lập thư mục lưu reports
        $this->uploadDir = __DIR__ . '/../../Views/uploads/';
        $this->uploadWebPath = '/Views/uploads';

        // Tạo thư mục nếu chưa có
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }

        // MIME types được phép (PRODUCTION MODE)
        $this->allowedMimes = [
            'image/png',
            'image/jpeg',
            'image/webp',
            'image/gif',
            'image/bmp',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain'
        ];

        // Map MIME -> extension
        $this->extMap = [
            'image/png' => 'png',
            'image/jpeg' => 'jpg',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/bmp' => 'bmp',
            'application/pdf' => 'pdf',
            'application/msword' => 'doc',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'docx',
            'application/vnd.ms-excel' => 'xls',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'xlsx',
            'text/plain' => 'txt',
        ];
    }

    /**
     * Upload file báo cáo (PDF, DOC, DOCX, JPG, PNG) - PRODUCTION MODE với security scan
     */
    public function uploadReport(string $fileField = 'report_file', string $baseName = '', int $maxSize = 5242880): array
    {
        $result = [
            'status' => 'error',
            'message' => '',
            'path' => '',
            'name' => '',
            'size' => 0,
            'mime' => ''
        ];

        // Kiểm tra file có tồn tại không
        if (empty($_FILES[$fileField]) || !is_array($_FILES[$fileField])) {
            $result['message'] = 'Không có file được tải lên.';
            return $result;
        }

        $file = $_FILES[$fileField];

        // Kiểm tra lỗi upload
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $result['message'] = $this->getUploadErrorMessage($file['error']);
            return $result;
        }

        // Kiểm tra kích thước
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0) {
            $result['message'] = 'File rỗng hoặc không hợp lệ.';
            return $result;
        }

        if ($size > $maxSize) {
            $result['message'] = 'File vượt quá ' . $this->formatSize($maxSize) . '.';
            return $result;
        }

        $tmpPath = (string) $file['tmp_name'];
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            $result['message'] = 'File tải lên không hợp lệ.';
            return $result;
        }

        // Scan malware/webshell TRƯỚC KHI xử lý
        $scanResult = $this->scanPhpMalware($tmpPath);
        if ($scanResult['isMalicious']) {
            @unlink($tmpPath);

            $result['message'] = '⚠️ CẢNH BÁO BẢO MẬT: File bị từ chối do chứa nội dung nguy hiểm.';

            // Log security alert
            error_log('[SECURITY ALERT] Malicious file upload blocked: ' . json_encode([
                'file' => $file['name'] ?? 'unknown',
                'patterns_found' => $scanResult['patternsFound'],
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                'timestamp' => date('Y-m-d H:i:s')
            ]));

            return $result;
        }

        // Xác định MIME type
        $mime = $this->getMimeType($tmpPath);
        if (!in_array($mime, $this->allowedMimes, true)) {
            $result['message'] = 'Loại file không được phép. Chỉ chấp nhận: JPG, PNG, GIF, WEBP, BMP, PDF, DOC, DOCX, XLS, XLSX, TXT';
            return $result;
        }

        // Kiểm tra magic bytes cho file ảnh
        if (strpos($mime, 'image/') === 0 && !$this->isValidImageFile($tmpPath)) {
            $result['message'] = 'File ảnh không hợp lệ (sai định dạng).';
            return $result;
        }

        // Tạo tên file an toàn
        $ext = $this->extMap[$mime] ?? 'bin';

        // Kiểm tra môi trường: production dùng tên ngẫu nhiên, development dùng baseName
        $isProduction = (getenv('APP_ENV') === 'production');

        if ($isProduction) {
            // Production: tên file ngẫu nhiên hoàn toàn để bảo mật
            $rand = bin2hex(random_bytes(16)); // 32 ký tự hex
            $fileName = $rand . '.' . $ext;
        } else {
            // Development: dùng baseName hoặc timestamp nếu không có
            if ($baseName !== '') {
                $fileName = $baseName . '.' . $ext;
            } else {
                $rand = bin2hex(random_bytes(6));
                $fileName = date('Ymd_His') . '_' . $rand . '.' . $ext;
            }
        }

        $destPath = $this->uploadDir . $fileName;

        // Di chuyển file
        $moved = false;
        if (@move_uploaded_file($tmpPath, $destPath)) {
            $moved = true;
        } else {
            // Fallback: thử copy
            if (@copy($tmpPath, $destPath)) {
                @unlink($tmpPath);
                $moved = true;
            }
        }

        if (!$moved) {
            $result['message'] = 'Không thể lưu file. Vui lòng kiểm tra quyền thư mục.';
            return $result;
        }

        // Trả kết quả thành công
        $webPath = $this->uploadWebPath . '/' . $fileName;
        $result['status'] = 'success';
        $result['message'] = 'Tải file thành công.';
        $result['path'] = $webPath;
        $result['name'] = $fileName;
        $result['size'] = $size;
        $result['mime'] = $mime;

        return $result;
    }

    /**
     * Upload file KHÔNG SCAN - CHỈ SỬ DỤNG CHO DEV MODE
     * Accept mọi loại file, không giới hạn MIME type
     */
    public function uploadWithoutScan(string $fileField = 'report_file', string $baseName = '', int $maxSize = 10485760): array
    {
        $result = [
            'status' => 'error',
            'message' => '',
            'path' => '',
            'name' => '',
            'size' => 0,
            'mime' => ''
        ];

        // Kiểm tra file có tồn tại không
        if (empty($_FILES[$fileField]) || !is_array($_FILES[$fileField])) {
            $result['message'] = 'Không có file được tải lên.';
            return $result;
        }

        $file = $_FILES[$fileField];

        // Kiểm tra lỗi upload
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            $result['message'] = $this->getUploadErrorMessage($file['error']);
            return $result;
        }

        // Kiểm tra kích thước
        $size = (int) ($file['size'] ?? 0);
        if ($size <= 0) {
            $result['message'] = 'File rỗng hoặc không hợp lệ.';
            return $result;
        }

        if ($size > $maxSize) {
            $result['message'] = 'File vượt quá ' . $this->formatSize($maxSize) . '.';
            return $result;
        }

        $tmpPath = (string) $file['tmp_name'];
        if ($tmpPath === '' || !is_uploaded_file($tmpPath)) {
            $result['message'] = 'File tải lên không hợp lệ.';
            return $result;
        }

        // Lấy MIME type (nhưng không filter)
        $mime = $this->getMimeType($tmpPath) ?? 'application/octet-stream';

        // Lấy extension từ tên file gốc
        $originalName = $file['name'] ?? 'unknown';
        $ext = pathinfo($originalName, PATHINFO_EXTENSION) ?: 'bin';

        // Tạo tên file
        if ($baseName !== '') {
            $fileName = $baseName . '.' . $ext;
        } else {
            $rand = bin2hex(random_bytes(6));
            $fileName = date('Ymd_His') . '_' . $rand . '.' . $ext;
        }

        $destPath = $this->uploadDir . $fileName;

        // Di chuyển file
        $moved = false;
        if (@move_uploaded_file($tmpPath, $destPath)) {
            $moved = true;
        } else {
            if (@copy($tmpPath, $destPath)) {
                @unlink($tmpPath);
                $moved = true;
            }
        }

        if (!$moved) {
            $result['message'] = 'Không thể lưu file. Vui lòng kiểm tra quyền thư mục.';
            return $result;
        }

        // Log warning
        error_log('[DEV MODE] File uploaded without security scan: ' . $fileName . ' (' . $mime . ')');

        // Trả kết quả thành công
        $webPath = $this->uploadWebPath . '/' . $fileName;
        $result['status'] = 'success';
        $result['message'] = 'Tải file thành công (DEV MODE - No Scan).';
        $result['path'] = $webPath;
        $result['name'] = $fileName;
        $result['size'] = $size;
        $result['mime'] = $mime;

        return $result;
    }

    /**
     * Bật DEV MODE - Cho phép upload và thực thi PHP files
     */
    public function enableDevMode(): array
    {
        $htaccessPath = $this->uploadDir . '.htaccess';
        $htaccessBackup = $this->uploadDir . '.htaccess.disabled';

        // Backup và đổi tên .htaccess để disable nó
        if (file_exists($htaccessPath)) {
            if (@rename($htaccessPath, $htaccessBackup)) {
                return ['success' => true, 'message' => '✅ DEV MODE enabled - .htaccess disabled'];
            } else {
                return ['success' => false, 'message' => '❌ Không thể disable .htaccess'];
            }
        }

        return ['success' => true, 'message' => '✅ DEV MODE enabled - No .htaccess found'];
    }

    /**
     * Tắt DEV MODE - Khôi phục bảo mật production
     */
    public function disableDevMode(): array
    {
        $htaccessPath = $this->uploadDir . '.htaccess';
        $htaccessBackup = $this->uploadDir . '.htaccess.disabled';

        // Tạo mới .htaccess production với bảo mật chặt chẽ
        $prodContent = <<<'HTACCESS'
# ========================================
# PRODUCTION MODE - MAXIMUM SECURITY
# ========================================

# VÔ HIỆU HÓA PHP EXECUTION
<IfModule mod_php.c>
    php_flag engine off
</IfModule>

RemoveHandler .php .phtml .php3 .php4 .php5 .php7 .phps .cgi .pl .py .jsp .asp .aspx .shtml .sh

# CHẶN THỰC THI PHP
<FilesMatch "\.(php|php3|php4|php5|php7|phtml|phps|pl|py|jsp|asp|aspx|sh|cgi|shtml)$">
    Require all denied
</FilesMatch>

# CHO PHÉP TẤT CẢ CÁC FILE KHÁC
<RequireAll>
    Require all granted
</RequireAll>

# BẢO MẬT
Options -Indexes -ExecCGI
HTACCESS;

        // Xóa backup cũ nếu có
        if (file_exists($htaccessBackup)) {
            @unlink($htaccessBackup);
        }

        // Tạo .htaccess mới
        if (@file_put_contents($htaccessPath, $prodContent) === false) {
            return ['success' => false, 'message' => '❌ Không thể tạo .htaccess bảo mật'];
        }

        // Verify file đã được tạo
        if (!file_exists($htaccessPath)) {
            return ['success' => false, 'message' => '❌ .htaccess không được tạo thành công'];
        }

        // Log security event
        error_log('[SECURITY] Dev Mode DISABLED - Production .htaccess created with maximum security at ' . date('Y-m-d H:i:s'));

        return ['success' => true, 'message' => '✅ DEV MODE disabled - Maximum security enabled'];
    }

    /**
     * Kiểm tra trạng thái DEV MODE
     */
    public function isDevModeEnabled(): bool
    {
        $htaccessPath = $this->uploadDir . '.htaccess';
        $htaccessBackup = $this->uploadDir . '.htaccess.disabled';

        // Nếu .htaccess bị disabled (đổi tên) = dev mode đang bật
        return !file_exists($htaccessPath) && file_exists($htaccessBackup);
    }

    /**
     * Xóa file báo cáo
     */
    public function deleteReport(string $fileName): array
    {
        $result = [
            'success' => false,
            'message' => ''
        ];

        if (empty($fileName)) {
            $result['message'] = 'Tên file không hợp lệ.';
            return $result;
        }

        // Chỉ cho phép xóa file trong thư mục uploads (security)
        $filePath = $this->uploadDir . basename($fileName);

        // Log để debug
        error_log('[DELETE FILE] Attempting to delete: ' . $filePath);

        if (!file_exists($filePath)) {
            error_log('[DELETE FILE] File does not exist: ' . $filePath);
            $result['message'] = 'File không tồn tại hoặc đã bị xóa.';
            $result['success'] = true; // Coi như thành công nếu file không tồn tại
            return $result;
        }

        if (!is_file($filePath)) {
            error_log('[DELETE FILE] Path is not a file: ' . $filePath);
            $result['message'] = 'Đường dẫn không phải là file.';
            return $result;
        }

        // Xóa file
        if (@unlink($filePath)) {
            error_log('[DELETE FILE] Successfully deleted: ' . $filePath);
            $result['success'] = true;
            $result['message'] = 'Xóa file thành công.';
        } else {
            error_log('[DELETE FILE] Failed to delete: ' . $filePath);
            $result['message'] = 'Không thể xóa file. Vui lòng kiểm tra quyền.';
        }

        return $result;
    }

    // ==================== PRIVATE METHODS ====================

    /**
     * Lấy MIME type của file
     */
    private function getMimeType(string $filePath): ?string
    {
        $fi = finfo_open(FILEINFO_MIME_TYPE);
        if ($fi === false) {
            return null;
        }
        $mime = finfo_file($fi, $filePath) ?: null;
        finfo_close($fi);
        return $mime;
    }

    /**
     * Lấy thông báo lỗi upload
     */
    private function getUploadErrorMessage(int $code): string
    {
        return match ($code) {
            UPLOAD_ERR_INI_SIZE => 'File vượt quá kích thước cho phép trong php.ini.',
            UPLOAD_ERR_FORM_SIZE => 'File vượt quá kích thước cho phép trong form.',
            UPLOAD_ERR_PARTIAL => 'File chỉ được tải lên một phần.',
            UPLOAD_ERR_NO_FILE => 'Không có file nào được tải lên.',
            UPLOAD_ERR_NO_TMP_DIR => 'Thiếu thư mục tạm.',
            UPLOAD_ERR_CANT_WRITE => 'Không thể ghi file lên đĩa.',
            UPLOAD_ERR_EXTENSION => 'Một extension PHP đã dừng việc tải file.',
            default => 'Lỗi không xác định khi tải file.'
        };
    }

    /**
     * Format kích thước file
     */
    private function formatSize(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;
        $size = (float) $bytes;
        while ($size >= 1024 && $i < count($units) - 1) {
            $size /= 1024;
            $i++;
        }
        return number_format($size, 2) . ' ' . $units[$i];
    }

    /**
     * Kiểm tra file ảnh hợp lệ bằng magic bytes
     */
    private function isValidImageFile(string $filePath): bool
    {
        $handle = @fopen($filePath, 'rb');
        if ($handle === false) {
            return false;
        }

        $bytes = fread($handle, 12);
        fclose($handle);

        if ($bytes === false || strlen($bytes) < 4) {
            return false;
        }

        // Kiểm tra magic bytes
        $validSignatures = [
            'png' => "\x89\x50\x4E\x47",
            'jpg' => "\xFF\xD8\xFF",
            'gif_87' => "GIF87a",
            'gif_89' => "GIF89a",
            'webp' => "RIFF",
        ];

        foreach ($validSignatures as $type => $signature) {
            if (strpos($bytes, $signature) === 0) {
                if ($type === 'webp' && strlen($bytes) >= 12) {
                    return substr($bytes, 8, 4) === 'WEBP';
                }
                return true;
            }
        }

        return false;
    }

    /**
     * Scan file để phát hiện PHP malware/webshell
     */
    private function scanPhpMalware(string $filePath): array
    {
        $result = [
            'isMalicious' => false,
            'patternsFound' => []
        ];

        $content = @file_get_contents($filePath);
        if ($content === false) {
            return $result;
        }

        // Kiểm tra xem file có phải text-based không
        $mime = $this->getMimeType($filePath);
        $isTextBased = in_array($mime, [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'text/plain',
            'text/html',
            'application/xml',
            'text/xml'
        ]);

        // Bỏ qua scan cho file ảnh thuần túy (binary)
        if (strpos($mime, 'image/') === 0) {
            // Chỉ kiểm tra xem có PHP tag rõ ràng ở đầu file không
            $header = substr($content, 0, 100);
            if (preg_match('/^<\?php/i', $header)) {
                $result['isMalicious'] = true;
                $result['patternsFound'][] = 'PHP code at file start';
            }
            return $result;
        }

        // Scan đầy đủ cho file text-based
        if ($isTextBased) {
            // Danh sách các pattern nguy hiểm
            $dangerousPatterns = [
                '/^<\?php/im' => 'PHP opening tag at line start',
                '/\beval\s*\(/i' => 'eval() function',
                '/\bbase64_decode\s*\(/i' => 'base64_decode() function',
                '/\bsystem\s*\(/i' => 'system() function',
                '/\bexec\s*\(/i' => 'exec() function',
                '/\bshell_exec\s*\(/i' => 'shell_exec() function',
                '/\bpassthru\s*\(/i' => 'passthru() function',
                '/\bproc_open\s*\(/i' => 'proc_open() function',
                '/\bassert\s*\(/i' => 'assert() function',
                '/file_get_contents\s*\(\s*["\']php:\/\/input["\']\s*\)/i' => 'php://input read',
                '/\bstr_rot13\s*\(/i' => 'str_rot13() obfuscation',
                '/\bgzinflate\s*\(/i' => 'gzinflate() obfuscation',
            ];

            foreach ($dangerousPatterns as $pattern => $description) {
                if (preg_match($pattern, $content)) {
                    $result['isMalicious'] = true;
                    $result['patternsFound'][] = $description;
                }
            }
        }

        return $result;
    }
}
