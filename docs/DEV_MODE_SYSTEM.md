# 🧪 Developer Mode System - TaskBB

## 📋 Tổng quan

Developer Mode System là một tính năng bảo mật cho phép **tạm thời vô hiệu hóa các ràng buộc upload** để phục vụ mục đích phát triển và testing, đồng thời đảm bảo **bảo mật tối đa** khi ở chế độ production.

### ⚡ Tính năng chính

- ✅ Toggle ON/OFF trực tiếp trên giao diện web
- ✅ Tự động đồng bộ trạng thái giữa client và server
- ✅ Real-time update không cần reload page
- ✅ Bảo mật đa lớp với RemoveHandler + FilesMatch
- ✅ Console logging để debug
- ✅ Toast notification thân thiện

---

## 🏗️ Kiến trúc hệ thống

### 1️⃣ **Frontend (UI Layer)**

#### File: `apps/Views/layouts/header_nnv.php`

**Vị trí:** Navbar chính
**Thành phần:**
```html
<div class="d-flex align-items-center px-3 py-2 rounded">
    <span><i class="bi bi-code-slash"></i> Developer Mode</span>
    <input type="checkbox" id="globalDevModeToggle">
    <span id="devModeStatusBadge">OFF</span>
</div>
```

**Chức năng:**
- Hiển thị toggle switch với badge ON/OFF
- Tự động kiểm tra trạng thái khi load trang
- Gọi API để toggle dev mode
- Cập nhật UI real-time
- Lưu trạng thái vào localStorage

**JavaScript Functions:**
```javascript
async function checkDevMode()     // Kiểm tra trạng thái hiện tại
toggle.addEventListener('change')  // Xử lý khi click toggle
window.isDevMode()                // Helper function cho các trang con
```

---

### 2️⃣ **Backend (API Layer)**

#### File: `apps/Controllers/TaskController.php`

**API Endpoints:**

| Action | Method | Mô tả |
|--------|--------|-------|
| `checkDevMode` | POST | Kiểm tra trạng thái dev mode hiện tại |
| `toggleDevMode` | POST | Bật/tắt dev mode |
| `sendReport` | POST | Upload file (tự động chọn mode) |

**Request/Response:**

```php
// checkDevMode
Request: { action: 'checkDevMode' }
Response: { success: true, enabled: boolean }

// toggleDevMode
Request: { action: 'toggleDevMode', enable: 'true'|'false' }
Response: { success: true, message: string }
```

---

### 3️⃣ **Core Logic (Upload Handler)**

#### File: `apps/Controllers/cUpload.php`

**Class Structure:**
```php
class cUpload {
    // Properties
    private string $uploadDir = 'Views/uploads/';
    private array $allowedMimes;
    private array $extMap;
    
    // Main Methods
    public function uploadReport()      // Production mode (with scan)
    public function uploadWithoutScan() // Dev mode (no restrictions)
    public function enableDevMode()     // Bật dev mode
    public function disableDevMode()    // Tắt dev mode
    public function isDevModeEnabled()  // Kiểm tra trạng thái
    
    // Security Methods
    private function scanPhpMalware()   // Scan malware/webshell
    private function isValidImageFile() // Validate image magic bytes
    private function getMimeType()      // Detect MIME type
}
```

---

## 🔐 Cơ chế bảo mật

### **Production Mode (Dev Mode OFF)**

#### .htaccess Configuration:

```apache
# 1. VÔ HIỆU HÓA HOÀN TOÀN PHP EXECUTION
<IfModule mod_php7.c>
    php_flag engine off
</IfModule>

RemoveHandler .php .phtml .php3 .php4 .php5 .php7 .phps .cgi .pl .py .jsp .asp .aspx .shtml .sh

# 2. CHẶN THỰC THI BẰNG FilesMatch (Lớp 2)
<FilesMatch "\.(php|php3|php4|php5|php7|phtml|phps|pl|py|jsp|asp|aspx|sh|cgi|shtml)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# 3. WHITELIST CHỈ CÁC FILE AN TOÀN
<FilesMatch "\.(jpg|jpeg|png|gif|webp|bmp|pdf|doc|docx|xls|xlsx|txt)$">
    Order Deny,Allow
    Allow from all
    Header set Content-Disposition "attachment"
    Header set X-Content-Type-Options "nosniff"
</FilesMatch>

# 4. CHẶN FILE NGUY HIỂM
<FilesMatch "\.(exe|bat|cmd|com|pif|scr|vbs|js|jar|zip|rar|sql|db|ini|log)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# 5. CHẶN FILE KHÔNG EXTENSION
<FilesMatch "^[^.]+$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# 6. BẢO MẬT CHỐNG DIRECTORY LISTING & TRAVERSAL
Options -Indexes -ExecCGI -Includes -FollowSymLinks

# 7. SECURITY HEADERS
Header set X-Content-Type-Options "nosniff"
Header set X-Frame-Options "SAMEORIGIN"
Header set X-XSS-Protection "1; mode=block"
Header set Content-Security-Policy "default-src 'none'; sandbox"
```

#### Upload Restrictions:

| Thuộc tính | Giá trị |
|------------|---------|
| Max size | 5MB |
| Allowed types | jpg, jpeg, png, gif, webp, bmp, pdf, doc, docx, xls, xlsx, txt |
| MIME check | ✅ Enabled |
| Magic bytes | ✅ Validated |
| Malware scan | ✅ Active |
| PHP execution | ❌ Blocked |

#### Malware Scan Patterns:

```php
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
```

---

### **Developer Mode (Dev Mode ON)**

#### Cấu hình:

| Thuộc tính | Giá trị |
|------------|---------|
| Max size | 10MB |
| Allowed types | ⚠️ TẤT CẢ |
| MIME check | ❌ Disabled |
| Magic bytes | ❌ Skipped |
| Malware scan | ❌ Disabled |
| PHP execution | ✅ Allowed |

#### Cơ chế hoạt động:

1. **Khi bật Dev Mode:**
   ```php
   rename('.htaccess', '.htaccess.disabled')
   ```
   - File .htaccess bị vô hiệu hóa
   - Apache không áp dụng restrictions
   - Tất cả file types được phép upload và execute

2. **Khi tắt Dev Mode:**
   ```php
   unlink('.htaccess.disabled')
   create_new_htaccess_with_maximum_security()
   ```
   - Xóa backup .htaccess.disabled
   - Tạo mới .htaccess với bảo mật tối đa
   - Khôi phục toàn bộ restrictions

---

## 📊 Flow Diagram

### Upload Flow:

```
┌─────────────────────────────────────────────┐
│  User clicks "Gửi báo cáo"                  │
└───────────────┬─────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────┐
│  Frontend checks: window.isDevMode()        │
└───────────────┬─────────────────────────────┘
                │
        ┌───────┴───────┐
        │               │
        ▼               ▼
┌──────────────┐  ┌──────────────────┐
│ Dev Mode ON  │  │ Dev Mode OFF     │
│ test_mode=1  │  │ test_mode=0      │
└──────┬───────┘  └────────┬─────────┘
       │                   │
       ▼                   ▼
┌──────────────┐  ┌──────────────────┐
│uploadWithout │  │ uploadReport()   │
│Scan()        │  │ (with security)  │
│              │  │                  │
│- No MIME     │  │- MIME check ✅   │
│  check       │  │- Magic bytes ✅  │
│- No scan     │  │- Malware scan ✅ │
│- 10MB limit  │  │- 5MB limit       │
│- All types ⚠️│  │- Whitelist only  │
└──────┬───────┘  └────────┬─────────┘
       │                   │
       └───────┬───────────┘
               │
               ▼
    ┌─────────────────────┐
    │ Save to Database    │
    │ Return success/error│
    └─────────────────────┘
```

### Toggle Flow:

```
┌─────────────────────────────────────────────┐
│  User clicks Developer Mode toggle          │
└───────────────┬─────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────┐
│  JavaScript: fetch toggleDevMode API        │
└───────────────┬─────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────┐
│  TaskController: action=toggleDevMode       │
└───────────────┬─────────────────────────────┘
                │
                ▼
┌─────────────────────────────────────────────┐
│  cUpload: enableDevMode() / disableDevMode()│
└───────────────┬─────────────────────────────┘
                │
        ┌───────┴───────┐
        │               │
        ▼               ▼
┌──────────────┐  ┌──────────────────┐
│ ENABLE       │  │ DISABLE          │
│              │  │                  │
│rename(       │  │unlink(           │
│ .htaccess,   │  │ .htaccess.       │
│ .htaccess.   │  │ disabled)        │
│ disabled)    │  │                  │
│              │  │create_new_       │
│              │  │htaccess()        │
└──────┬───────┘  └────────┬─────────┘
       │                   │
       └───────┬───────────┘
               │
               ▼
    ┌─────────────────────┐
    │ Return success/error│
    │ Update UI (badge,   │
    │ toast, localStorage)│
    └─────────────────────┘
```

---

## 🛠️ API Reference

### checkDevMode

**Endpoint:** `TaskController.php?action=checkDevMode`

**Method:** POST

**Response:**
```json
{
  "success": true,
  "enabled": true
}
```

**Usage:**
```javascript
const formData = new FormData();
formData.append('action', 'checkDevMode');
const res = await fetch(url, { method: 'POST', body: formData });
const json = await res.json();
console.log('Dev Mode:', json.enabled ? 'ON' : 'OFF');
```

---

### toggleDevMode

**Endpoint:** `TaskController.php?action=toggleDevMode`

**Method:** POST

**Parameters:**
- `enable` (string): `'true'` hoặc `'false'`

**Response:**
```json
{
  "success": true,
  "message": "✅ DEV MODE enabled - .htaccess disabled"
}
```

**Usage:**
```javascript
const formData = new FormData();
formData.append('action', 'toggleDevMode');
formData.append('enable', 'true');
const res = await fetch(url, { method: 'POST', body: formData });
const json = await res.json();
alert(json.message);
```

---

### sendReport (Auto-detect mode)

**Endpoint:** `TaskController.php?action=sendReport`

**Method:** POST (multipart/form-data)

**Parameters:**
- `task_id` (int): ID của task
- `report_content` (string): Nội dung báo cáo
- `report_file` (file): File đính kèm
- `test_mode` (string): `'0'` hoặc `'1'` (auto-set by frontend)

**Response:**
```json
{
  "success": true,
  "message": "Gửi báo cáo thành công!"
}
```

---

## 📁 Cấu trúc thư mục

```
taskbb/
├── apps/
│   ├── Controllers/
│   │   ├── cUpload.php           # Core upload handler
│   │   ├── TaskController.php    # API endpoints
│   │   └── DevModeManager.php    # CLI tool (optional)
│   └── Views/
│       ├── layouts/
│       │   └── header_nnv.php    # Dev Mode toggle UI
│       └── remote_nnv/
│           └── task_report.php   # Upload modal
├── Views/
│   └── uploads/
│       ├── .htaccess             # Production security
│       ├── .htaccess.disabled    # Dev mode (when ON)
│       ├── .htaccess.production  # Backup (auto-created)
│       └── *.* (uploaded files)
└── docs/
    └── DEV_MODE_SYSTEM.md        # This file
```

---

## 🔧 Configuration

### Allowed MIME Types (Production):

```php
private array $allowedMimes = [
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
```

### File Extensions Map:

```php
private array $extMap = [
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
    'text/plain' => 'txt'
];
```

---

## 🧪 Testing Guide

### Test Case 1: Upload file an toàn (Production Mode)

1. Đảm bảo Dev Mode **OFF**
2. Upload file: `test.pdf`
3. Expected: ✅ Upload thành công
4. Verify: File được lưu vào `Views/uploads/`

### Test Case 2: Upload file PHP (Production Mode)

1. Đảm bảo Dev Mode **OFF**
2. Upload file: `shell.php`
3. Expected: ❌ File bị từ chối
4. Message: "Loại file không được phép"

### Test Case 3: Upload file PHP (Dev Mode ON)

1. Bật Dev Mode **ON**
2. Upload file: `shell.php`
3. Expected: ✅ Upload thành công
4. Verify: File được lưu và execute được

### Test Case 4: Toggle Dev Mode

1. Click toggle từ OFF → ON
2. Expected: Toast "Dev Mode ENABLED" xuất hiện
3. Verify: Badge hiển thị "ON" (màu đỏ)
4. Check: `.htaccess` → `.htaccess.disabled`

5. Click toggle từ ON → OFF
6. Expected: Toast "Dev Mode DISABLED" xuất hiện
7. Verify: Badge hiển thị "OFF" (màu xám)
8. Check: `.htaccess` được tạo mới với RemoveHandler

---

## ⚠️ Security Warnings

### 🚨 QUAN TRỌNG

1. **KHÔNG BAO GIỜ** bật Dev Mode trên production server
2. **CHỈ SỬ DỤNG** trên localhost hoặc development environment
3. **TẮT NGAY** Dev Mode sau khi hoàn thành testing
4. Kiểm tra file `.htaccess` trước khi deploy

### 🔒 Best Practices

- ✅ Luôn verify trạng thái dev mode trước khi deploy
- ✅ Log tất cả các lần toggle dev mode
- ✅ Giới hạn quyền toggle dev mode cho admin only
- ✅ Thêm IP whitelist nếu cần thiết
- ✅ Monitor file uploads trong dev mode

---

## 📝 Changelog

### Version 1.0.0 (2025-12-18)

**Features:**
- ✅ Dev Mode toggle UI trên navbar
- ✅ Tự động sync trạng thái client/server
- ✅ RemoveHandler + FilesMatch bảo mật đa lớp
- ✅ Malware scanning với 12+ patterns
- ✅ Toast notifications
- ✅ Console logging
- ✅ localStorage persistence

**Security Enhancements:**
- ✅ Magic bytes validation cho images
- ✅ Content-Disposition: attachment header
- ✅ Content-Security-Policy sandbox
- ✅ Directory traversal protection
- ✅ File extension blacklist

---

## 👨‍💻 Maintenance

### Log Files

Tất cả events được log vào PHP error_log:

```bash
# Dev mode events
[DEV MODE] File uploaded without security scan: file.php (text/x-php)

# Security events
[SECURITY] Dev Mode DISABLED - Production .htaccess created at 2025-12-18 10:30:00
[SECURITY ALERT] Malicious file upload blocked: {...}

# File operations
[DELETE FILE] Successfully deleted: /path/to/file.pdf
```

### Troubleshooting

**Problem:** Toggle không hoạt động

**Solution:**
1. Check console: `F12 → Console`
2. Verify API endpoint: `TaskController.php` accessible
3. Check .htaccess permissions: `chmod 644`

**Problem:** File PHP vẫn execute được sau khi tắt dev mode

**Solution:**
1. Verify `.htaccess` tồn tại trong `Views/uploads/`
2. Check Apache config: `AllowOverride All`
3. Restart Apache

**Problem:** Upload fail với dev mode ON

**Solution:**
1. Check folder permissions: `chmod 777 Views/uploads/`
2. Verify PHP upload_max_filesize >= 10M
3. Check error_log

---

## 📞 Support

- **Developer:** Lê Trọng Duy
- **Project:** TaskBB - Task Management System
- **Date:** December 18, 2025

---

**⚠️ LƯU Ý CUỐI CÙNG:**

Dev Mode System là công cụ mạnh mẽ nhưng cực kỳ nguy hiểm nếu sử dụng sai cách. **LUÔN LUÔN** tắt dev mode trước khi deploy lên production!

---

*End of Documentation*
