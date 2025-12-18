# 📚 TaskBB - Hệ thống quản lý công việc

## 🎯 Tổng quan

TaskBB là hệ thống quản lý công việc (Task Management System) được xây dựng để quản lý dự án và phân công công việc giữa các thành viên trong tổ chức.

### Phân quyền hệ thống

Hệ thống có **3 vai trò chính**:

| Vai trò | Viết tắt | Mô tả |
|---------|----------|-------|
| **Administrator** | Admin | Quản trị viên - Quản lý toàn bộ hệ thống |
| **Người giao việc** | NGV | Tạo dự án, giao việc, theo dõi tiến độ, đánh giá kết quả |
| **Người nhận việc** | NNV | Nhận việc, thực hiện, báo cáo kết quả |

---

## 👨‍💼 ADMINISTRATOR (Admin)

### 🔑 Quyền hạn
- Quản lý toàn bộ người dùng (CRUD users)
- Xem tất cả dự án và công việc
- Xem lịch sử công việc của mọi người
- Quản lý phân quyền
- Truy cập chế độ Developer Mode

### 📋 Chức năng chính

#### 1. Quản lý người dùng
**Đường dẫn:** `apps/Views/users/`

**Các tính năng:**
- ✅ Tạo người dùng mới (`create.php`)
- ✅ Chỉnh sửa thông tin người dùng (`edit.php`)
- ✅ Xóa người dùng
- ✅ Xem danh sách người dùng (`index.php`)
- ✅ Phân quyền: Admin / NGV / NNV

**Form tạo người dùng:**
```php
- Họ và tên
- Email (unique)
- Mật khẩu
- Vai trò (Role)
- Trạng thái (Active/Inactive)
```

#### 2. Giám sát công việc
**Đường dẫn:** `apps/Views/remote_admin/`

**Các tính năng:**
- ✅ Xem tất cả công việc được giao (`task_assigned.php`)
- ✅ Xem lịch sử hoàn thành công việc (`history.php`)
- ✅ Xem danh sách công việc theo trạng thái:
  - Pending (Chờ xử lý)
  - In Progress (Đang thực hiện)
  - Completed (Hoàn thành)
  - Overdue (Quá hạn)

#### 3. Dashboard
**Đường dẫn:** `apps/Views/admin/index.php`

**Thống kê hiển thị:**
- 📊 Tổng số dự án
- 📊 Tổng số công việc
- 📊 Tổng số người dùng
- 📊 Công việc theo trạng thái
- 📊 Biểu đồ tiến độ

---

## 👤 NGƯỜI GIAO VIỆC (NGV)

### 🔑 Quyền hạn
- Tạo và quản lý dự án
- Tạo và phân công công việc
- Theo dõi tiến độ công việc
- Xem và đánh giá báo cáo
- Review và chấm điểm công việc

### 📋 Chức năng chính

#### 1. Quản lý dự án
**Đường dẫn:** `apps/Views/projects/`

**Các tính năng:**
- ✅ Tạo dự án mới (`create.php`)
  - Tên dự án
  - Mô tả
  - Ngày bắt đầu / Ngày kết thúc
  - Trạng thái
  
- ✅ Chỉnh sửa dự án (`edit.php`)
- ✅ Xóa dự án
- ✅ Xem danh sách dự án (`index.php`)
- ✅ Xem tiến độ dự án (%)

**Controller:** `ProjectController.php`

#### 2. Quản lý công việc (Tasks)
**Đường dẫn:** `apps/Views/task/`

**Tạo công việc mới** (`create.php`):
```php
- Tiêu đề công việc
- Mô tả chi tiết
- Dự án (Project)
- Người nhận việc (Assignee)
- Hạn hoàn thành (Deadline)
- Độ ưu tiên (Priority): High / Medium / Low
```

**Chỉnh sửa công việc** (`edit.php`):
- Cập nhật thông tin công việc
- Thay đổi người nhận việc
- Điều chỉnh deadline
- Cập nhật trạng thái

**Xem chi tiết công việc** (`detail.php`):
- Thông tin công việc đầy đủ
- Lịch sử thay đổi
- Báo cáo đính kèm
- Kết quả đánh giá

**Lịch sử công việc** (`history.php`):
- Xem tất cả công việc đã hoàn thành
- Lọc theo dự án
- Lọc theo người thực hiện
- Xem timeline

**Controller:** `TaskController.php`

#### 3. Theo dõi tiến độ
**Đường dẫn:** `apps/Views/remote_ngv/`

**Dashboard NGV** (`index.php`):
- Tổng quan dự án đang quản lý
- Thống kê công việc theo trạng thái
- Công việc sắp đến hạn
- Công việc quá hạn

**Công việc đã giao** (`task_assigned.php`):
- Danh sách tất cả công việc đã giao
- Trạng thái realtime
- Progress bar từng công việc
- Action: Chỉnh sửa / Xóa

**Dự án** (`projects.php`):
- Danh sách dự án quản lý
- Tiến độ từng dự án
- Số lượng task trong mỗi dự án

#### 4. Review và đánh giá
**Đường dẫn:** `apps/Views/task/review_detail.php`

**Chức năng review:**
- ✅ Xem báo cáo từ NNV
- ✅ Download file đính kèm
- ✅ Đánh giá kết quả:
  - **Đạt yêu cầu** → Task completed
  - **Chưa đạt** → Task broken (yêu cầu làm lại)
- ✅ Ghi chú phản hồi

**Báo cáo** (`apps/Views/remote_ngv/reports.php`):
- Xem tất cả báo cáo đã nhận
- Lọc theo dự án
- Lọc theo trạng thái
- Export báo cáo

---

## 🧑‍💻 NGƯỜI NHẬN VIỆC (NNV)

### 🔑 Quyền hạn
- Xem công việc được giao
- Cập nhật trạng thái công việc
- Gửi báo cáo hoàn thành
- Xem lịch sử công việc đã làm

### 📋 Chức năng chính

#### 1. Dashboard NNV
**Đường dẫn:** `apps/Views/nnv/index.php`

**Hiển thị:**
- 📊 Tổng số công việc đang làm
- 📊 Công việc hoàn thành
- 📊 Công việc quá hạn
- 📊 Task sắp đến deadline

#### 2. Nhận và thực hiện công việc
**Đường dẫn:** `apps/Views/remote_nnv/task_receive.php`

**Kanban Board - 4 cột:**

| Pending ⏳ | In Progress ⚙️ | Completed ✅ | Overdue ⚠️ |
|-----------|---------------|-------------|-----------|
| Công việc mới nhận | Đang thực hiện | Hoàn thành | Quá hạn |

**Tính năng:**
- ✅ Drag & Drop công việc giữa các cột
- ✅ Tự động cập nhật trạng thái khi kéo
- ✅ Real-time progress update
- ✅ Click vào task để xem chi tiết

**Thư viện:** SortableJS

**Code mẫu:**
```javascript
const sortables = document.querySelectorAll('.kanban-column');
sortables.forEach(col => {
    new Sortable(col, {
        group: 'shared',
        animation: 150,
        onEnd: function(evt) {
            updateTaskStatus(taskId, newStatus);
        }
    });
});
```

#### 3. Gửi báo cáo
**Đường dẫn:** `apps/Views/remote_nnv/task_report.php`

**Modal gửi báo cáo:**

**Form fields:**
```php
- Chọn Task (dropdown - chỉ hiển thị task Completed)
- Nội dung báo cáo (textarea)
- File đính kèm (file upload)
  - Production Mode: Max 5MB
  - Dev Mode: Max 10MB
```

**Allowed file types (Production):**
- 📄 Documents: PDF, DOC, DOCX, XLS, XLSX, TXT
- 🖼️ Images: JPG, PNG, GIF, WEBP, BMP

**Developer Mode:**
- 🚨 Chấp nhận TẤT CẢ file types (kể cả .php)
- 🚨 Không scan malware
- 🚨 CHỈ dùng cho testing

**Upload flow:**
```
User submits → Check dev mode → 
  If ON: uploadWithoutScan() 
  If OFF: uploadReport() (with security scan) 
→ Save to database → Reload page
```

**Controller:** `TaskController.php::sendReport()`

#### 4. Công việc bị từ chối
**Đường dẫn:** `apps/Views/remote_nnv/task_receive_broken.php`

**Chức năng:**
- ✅ Xem danh sách task bị reject
- ✅ Xem lý do từ chối từ NGV
- ✅ Làm lại và gửi báo cáo mới
- ✅ Xem feedback chi tiết

#### 5. Lịch sử công việc
**Đường dẫn:** `apps/Views/remote_nnv/task_history.php`

**Hiển thị:**
- Tất cả công việc đã hoàn thành
- Timeline hoàn thành
- Kết quả đánh giá
- File báo cáo đã gửi

---

## 🛠️ CẤU TRÚC HỆ THỐNG

### Backend Structure

```
apps/
├── Controllers/
│   ├── AuthController.php      # Xác thực, đăng nhập
│   ├── UserController.php      # Quản lý người dùng
│   ├── ProjectController.php   # Quản lý dự án
│   ├── TaskController.php      # Quản lý công việc
│   ├── ReportController.php    # Quản lý báo cáo
│   ├── cUpload.php            # Upload file handler
│   └── DevModeManager.php     # Dev mode CLI
│
├── Models/
│   ├── DatabaseModel.php      # Kết nối database
│   ├── UserModel.php          # User CRUD
│   ├── ProjectModel.php       # Project CRUD
│   ├── TaskModel.php          # Task CRUD
│   └── ReportModel.php        # Report CRUD
│
└── Views/
    ├── admin/                 # Admin views
    ├── ngv/                   # NGV views
    ├── nnv/                   # NNV views
    ├── layouts/               # Header, sidebar, footer
    ├── remote_admin/          # Admin remote actions
    ├── remote_ngv/            # NGV remote actions
    └── remote_nnv/            # NNV remote actions
```

### Database Schema

**Bảng chính:**

#### users
```sql
- user_id (PK)
- username
- email
- password
- full_name
- role (admin/ngv/nnv)
- avatar
- created_at
```

#### projects
```sql
- project_id (PK)
- name
- description
- start_date
- end_date
- status
- created_by (FK → users)
- progress (%)
- created_at
```

#### tasks
```sql
- task_id (PK)
- project_id (FK → projects)
- title
- description
- assigned_to (FK → users)
- created_by (FK → users)
- deadline
- priority (High/Medium/Low)
- status (Pending/In Progress/Completed/Overdue)
- result (Pass/Broken)
- created_at
- updated_at
```

#### reports
```sql
- report_id (PK)
- task_id (FK → tasks)
- user_id (FK → users)
- content
- file_path
- file_original_name
- created_at
```

---

## 🔄 LUỒNG CÔNG VIỆC (Workflow)

### 1. NGV tạo dự án và giao việc

```
NGV → Tạo Project → Thêm Tasks → Phân công cho NNV
```

**Chi tiết:**
1. NGV đăng nhập
2. Vào "Quản lý dự án" → Click "Tạo dự án mới"
3. Điền thông tin: Tên, Mô tả, Ngày bắt đầu/kết thúc
4. Sau khi tạo project → Vào "Tạo công việc"
5. Chọn Project, nhập Title, Description, Deadline, Priority
6. Chọn người nhận việc (NNV)
7. Submit → Task được giao

### 2. NNV nhận và thực hiện việc

```
NNV → Xem task → Kéo sang "In Progress" → Thực hiện → 
Kéo sang "Completed" → Gửi báo cáo
```

**Chi tiết:**
1. NNV đăng nhập
2. Vào "Công việc được giao"
3. Xem task ở cột "Pending"
4. Kéo task sang "In Progress" → Trạng thái tự động update
5. Hoàn thành công việc → Kéo sang "Completed"
6. Click "Gửi báo cáo" → Chọn task vừa hoàn thành
7. Điền nội dung báo cáo
8. Upload file đính kèm (PDF, DOC, ảnh...)
9. Submit → Báo cáo được gửi đến NGV

### 3. NGV review và đánh giá

```
NGV → Xem báo cáo → Review → 
  ✅ Đạt → Task done
  ❌ Chưa đạt → Task broken → NNV làm lại
```

**Chi tiết:**
1. NGV vào "Báo cáo"
2. Xem báo cáo từ NNV
3. Download file đính kèm (nếu có)
4. Review kết quả:
   - Click "Đạt yêu cầu" → Task hoàn thành
   - Click "Chưa đạt" → Task broken, NNV phải làm lại
5. Ghi chú phản hồi cho NNV

### 4. Xử lý task bị từ chối

```
NNV → Xem task broken → Xem feedback NGV → 
Làm lại → Gửi báo cáo mới → NGV review lại
```

---

## 🎨 GIAO DIỆN NGƯỜI DÙNG

### Theme và màu sắc

| Màu sắc | Hex | Sử dụng |
|---------|-----|---------|
| Primary Blue | `#0d6efd` | Buttons, links |
| Success Green | `#198754` | Completed tasks |
| Warning Yellow | `#ffc107` | Pending tasks |
| Danger Red | `#dc3545` | Overdue, Dev Mode ON |
| Secondary Gray | `#6c757d` | Disabled, Dev Mode OFF |
| Dark Navy | `#212529` | Navbar, sidebar |

### Components

**1. Navbar**
- Logo TaskBB
- Developer Mode Toggle (chỉ hiển thị khi logged in)
- User info dropdown
- Logout button

**2. Sidebar**
- Menu điều hướng theo role
- Active state highlight
- Icons Bootstrap

**3. Cards**
- Task cards với drag & drop
- Project cards với progress bar
- User cards trong admin panel

**4. Modals**
- Task detail modal
- Report submission modal
- Confirmation dialogs

**5. Forms**
- Validation frontend (required fields)
- Validation backend
- Error messages

---

## 🔐 BẢO MẬT

### Authentication

**Login system:**
- Email + Password
- Session-based authentication
- Password hashing: `password_hash()` với `PASSWORD_DEFAULT`

**Session management:**
```php
session_start();
$_SESSION['user_id'] = $userId;
$_SESSION['role'] = $userRole;
$_SESSION['username'] = $username;
```

### Authorization

**Role-based access control:**

```php
// Check admin
if ($_SESSION['role'] !== 'admin') {
    header('Location: /unauthorized');
    exit;
}

// Check NGV
if (!in_array($_SESSION['role'], ['admin', 'ngv'])) {
    header('Location: /unauthorized');
    exit;
}
```

### File Upload Security

**Production Mode (Dev Mode OFF):**
- ✅ MIME type validation
- ✅ File extension whitelist
- ✅ Magic bytes verification
- ✅ Malware/webshell scanning (12+ patterns)
- ✅ Max file size: 5MB
- ✅ `.htaccess` với `RemoveHandler` PHP
- ✅ Force download với `Content-Disposition: attachment`

**Dev Mode (ONLY for testing):**
- ⚠️ Accept all file types
- ⚠️ No malware scan
- ⚠️ Max file size: 10MB
- ⚠️ PHP execution allowed

**Chi tiết:** Xem `docs/DEV_MODE_SYSTEM.md`

### SQL Injection Prevention

**Sử dụng PDO với prepared statements:**

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
```

### XSS Prevention

**Escape output:**
```php
<?= htmlspecialchars($userName) ?>
```

---

## 📊 TRACKING & LOGGING

### Activity Logs

**Các events được log:**
```php
// Dev mode events
[DEV MODE] File uploaded without security scan: file.php

// Security alerts
[SECURITY ALERT] Malicious file upload blocked: {...}

// File operations
[DELETE FILE] Successfully deleted: /path/to/file.pdf

// Task updates
[TASK UPDATE] Task #23 status changed: Pending → In Progress
```

### Error Handling

**Frontend:**
```javascript
try {
    const res = await fetch(url);
    const json = await res.json();
    if (!json.success) {
        alert(json.message);
    }
} catch (err) {
    console.error(err);
    alert('Đã xảy ra lỗi!');
}
```

**Backend:**
```php
try {
    $result = $taskModel->addTask(...);
    echo json_encode(['success' => true, 'message' => 'Thành công']);
} catch (Exception $e) {
    error_log($e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Lỗi']);
}
```

---

## 🚀 DEPLOYMENT

### Requirements

- **Web Server:** Apache 2.4+
- **PHP:** 7.4+ (hoặc 8.0+)
- **Database:** MySQL 5.7+ / MariaDB 10.3+
- **Extensions:**
  - PDO
  - pdo_mysql
  - fileinfo
  - gd (cho xử lý ảnh)

### Apache Modules (Required)

```apache
- mod_rewrite (URL rewriting)
- mod_headers (Security headers)
- mod_php (PHP processing)
```

### Installation

1. **Clone/Copy project vào web root:**
   ```bash
   cp -r taskbb /var/www/html/
   ```

2. **Import database:**
   ```bash
   mysql -u root -p < taskbb_complete_database.sql
   ```

3. **Configure database:**
   Edit `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'taskbb');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   ```

4. **Set permissions:**
   ```bash
   chmod 755 -R taskbb/
   chmod 777 -R taskbb/Views/uploads/
   chmod 777 -R taskbb/public/uploads/
   ```

5. **Create .htaccess in uploads:**
   ```bash
   cp Views/uploads/.htaccess.production Views/uploads/.htaccess
   ```

6. **Access:**
   ```
   http://localhost/taskbb
   ```

### Default Admin Account

```
Email: admin@taskbb.com
Password: admin123
```

**⚠️ QUAN TRỌNG:** Đổi password ngay sau khi login lần đầu!

---

## 📝 API ENDPOINTS

### Authentication

| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/apps/Controllers/AuthController.php?action=login` | POST | Đăng nhập |
| `/apps/Controllers/AuthController.php?action=logout` | GET | Đăng xuất |
| `/apps/Controllers/AuthController.php?action=changePassword` | POST | Đổi mật khẩu |

### Users (Admin only)

| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/apps/Controllers/UserController.php?action=create` | POST | Tạo user |
| `/apps/Controllers/UserController.php?action=update` | POST | Cập nhật user |
| `/apps/Controllers/UserController.php?action=delete` | POST | Xóa user |
| `/apps/Controllers/UserController.php?action=getAll` | GET | Lấy danh sách users |

### Projects

| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/apps/Controllers/ProjectController.php?action=create` | POST | Tạo project |
| `/apps/Controllers/ProjectController.php?action=update` | POST | Cập nhật project |
| `/apps/Controllers/ProjectController.php?action=delete` | POST | Xóa project |
| `/apps/Controllers/ProjectController.php?action=getAll` | GET | Lấy danh sách projects |

### Tasks

| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/apps/Controllers/TaskController.php?action=addTask` | POST | Tạo task |
| `/apps/Controllers/TaskController.php?action=updateTask` | POST | Cập nhật task |
| `/apps/Controllers/TaskController.php?action=delete` | POST | Xóa task |
| `/apps/Controllers/TaskController.php?action=changeStatus` | POST | Đổi trạng thái task |
| `/apps/Controllers/TaskController.php?action=sendReport` | POST | Gửi báo cáo |
| `/apps/Controllers/TaskController.php?action=saveTaskReview` | POST | Lưu đánh giá task |

### Developer Mode

| Endpoint | Method | Mô tả |
|----------|--------|-------|
| `/apps/Controllers/TaskController.php?action=checkDevMode` | POST | Kiểm tra trạng thái dev mode |
| `/apps/Controllers/TaskController.php?action=toggleDevMode` | POST | Bật/tắt dev mode |

---

## 🐛 TROUBLESHOOTING

### Common Issues

**1. File upload không hoạt động**

**Nguyên nhân:**
- Folder permissions không đủ
- PHP upload_max_filesize quá nhỏ
- .htaccess block file types

**Giải pháp:**
```bash
# Check permissions
chmod 777 Views/uploads/

# Edit php.ini
upload_max_filesize = 10M
post_max_size = 10M

# Bật dev mode tạm thời
# Navbar → Toggle Developer Mode ON
```

**2. Dev Mode toggle không hoạt động**

**Nguyên nhân:**
- JavaScript error
- API endpoint không accessible

**Giải pháp:**
```bash
# Check console (F12)
# Verify TaskController.php có action checkDevMode và toggleDevMode

# Test manually:
php apps/Controllers/DevModeManager.php status
```

**3. Session timeout quá nhanh**

**Giải pháp:**
Edit `php.ini`:
```ini
session.gc_maxlifetime = 3600
session.cookie_lifetime = 0
```

**4. Database connection error**

**Giải pháp:**
```php
// Check config/database.php
// Verify MySQL service running
sudo service mysql start

// Test connection
mysql -u root -p -e "SHOW DATABASES;"
```

---

## 📞 SUPPORT

### Contact

- **Developer:** Lê Trọng Duy
- **Email:** letrongduy@taskbb.com
- **Project:** TaskBB v1.0
- **Date:** December 18, 2025

### Documentation Files

- `DEV_MODE_SYSTEM.md` - Chi tiết Developer Mode System
- `SYSTEM_FEATURES.md` - Tài liệu này
- `README.md` - Quick start guide

---

## 🔄 FUTURE ENHANCEMENTS

### Planned Features

- [ ] **Notifications system**
  - Email notifications khi được giao việc
  - Real-time notifications (WebSocket/Pusher)
  - In-app notification center

- [ ] **Comments & Discussion**
  - Comment trên từng task
  - Tag người dùng (@mention)
  - Thread discussions

- [ ] **File versioning**
  - Lưu nhiều version của báo cáo
  - Compare versions
  - Rollback to previous version

- [ ] **Advanced reporting**
  - Export reports to Excel/PDF
  - Dashboard với charts (Chart.js)
  - Performance analytics

- [ ] **Mobile app**
  - React Native mobile app
  - Push notifications
  - Offline mode

- [ ] **Integrations**
  - Google Calendar sync
  - Slack integration
  - GitHub integration

- [ ] **Advanced permissions**
  - Custom roles
  - Team-based permissions
  - Project-level access control

---

**Last Updated:** December 18, 2025  
**Version:** 1.0.0  
**Status:** ✅ Production Ready (with Dev Mode for testing)

---

*End of System Features Documentation*
