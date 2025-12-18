# Hướng dẫn Triển khai TaskBB

## 1. Tính năng tự động phát hiện App Root

Hệ thống TaskBB được thiết kế để **tự động phát hiện đường dẫn gốc** của ứng dụng, cho phép triển khai linh hoạt trên nhiều môi trường khác nhau mà không cần cấu hình phức tạp.

### Cách hoạt động:

1. **Ưu tiên từ file .env**: Nếu có `APP_URL` trong file `.env`, hệ thống sẽ sử dụng giá trị này
2. **Tự động phát hiện**: Nếu không có trong `.env`, hệ thống tự động tính toán dựa trên:
   - Protocol (HTTP/HTTPS)
   - Hostname
   - Đường dẫn thư mục chứa project

### Các trường hợp triển khai:

#### A. Trên XAMPP/WAMP (localhost)

**Trường hợp 1: Thư mục gốc**
```
Đường dẫn: C:\xampp\htdocs\taskbb
URL truy cập: http://localhost/taskbb
→ Tự động phát hiện: http://localhost/taskbb
```

**Trường hợp 2: Trong htdocs**
```
Đường dẫn: C:\xampp\htdocs\myproject\taskbb
URL truy cập: http://localhost/myproject/taskbb
→ Tự động phát hiện: http://localhost/myproject/taskbb
```

**Trường hợp 3: Virtual Host**
```
Đường dẫn: C:\xampp\htdocs\taskbb
Virtual Host: taskbb.local
URL truy cập: http://taskbb.local
→ Cần cấu hình .env: APP_URL=http://taskbb.local
```

#### B. Trên Production Server

**Trường hợp 1: Domain chính**
```
Đường dẫn: /var/www/html
URL truy cập: https://yourdomain.com
→ Cấu hình .env: APP_URL=https://yourdomain.com
```

**Trường hợp 2: Subdirectory**
```
Đường dẫn: /var/www/html/taskbb
URL truy cập: https://yourdomain.com/taskbb
→ Tự động phát hiện: https://yourdomain.com/taskbb
```

**Trường hợp 3: Subdomain**
```
Đường dẫn: /var/www/html
URL truy cập: https://tasks.yourdomain.com
→ Cấu hình .env: APP_URL=https://tasks.yourdomain.com
```

## 2. Cài đặt và Cấu hình

### Bước 1: Copy file cấu hình
```bash
cp .env.example .env
```

### Bước 2: Chỉnh sửa .env (tùy chọn)

**Để ứng dụng tự động phát hiện (khuyến nghị cho development):**
```env
APP_URL=
```

**Hoặc cấu hình cụ thể (khuyến nghị cho production):**
```env
APP_URL=http://localhost/taskbb
# hoặc
APP_URL=https://yourdomain.com
```

### Bước 3: Cấu hình Database
```env
DB_HOST=localhost
DB_NAME=taskbb
DB_USER=root
DB_PASS=
```

### Bước 4: Import Database
```bash
# Sử dụng file SQL mới nhất
mysql -u root -p taskbb < taskbb1_latest.sql
```

## 3. Kiểm tra Cấu hình

### Debug BASE_URL
Thêm dòng này vào file bất kỳ để kiểm tra:
```php
<?php
require_once 'config/config.php';
echo "BASE_URL: " . BASE_URL;
?>
```

### JavaScript Console
Mở browser console và kiểm tra:
```javascript
console.log('BASE_URL:', window.BASE_URL);
console.log('APP_BASE_URL:', window.APP_BASE_URL);
```

## 4. Các biến toàn cục

### PHP
- `BASE_URL` - URL gốc của ứng dụng
- `BASE_PATH` - Đường dẫn vật lý đến thư mục gốc
- `CSS_URL` - URL đến thư mục CSS
- `JS_URL` - URL đến thư mục JS
- `UPLOAD_URL` - URL đến thư mục uploads

### JavaScript
- `window.BASE_URL` - URL gốc (khuyến nghị sử dụng)
- `window.APP_BASE_URL` - Alias của BASE_URL (để tương thích ngược)
- `window.url(path)` - Helper function tạo URL
- `window.asset(path)` - Helper function tạo asset URL

## 5. Best Practices

### Trong PHP
```php
// Tạo URL
echo url('apps/Views/auth/login.php');
// → http://localhost/taskbb/apps/Views/auth/login.php

// Asset URL
echo asset('css/style.css');
// → http://localhost/taskbb/public/css/style.css
```

### Trong JavaScript
```javascript
// Tạo URL
const loginUrl = url('apps/Views/auth/login.php');

// Asset URL  
const cssUrl = asset('css/style.css');

// AJAX request
fetch(url('apps/Controllers/TaskController.php'))
```

## 6. Troubleshooting

### Lỗi: CSS/JS không load
- Kiểm tra `BASE_URL` có đúng không
- Kiểm tra file `.htaccess` trong thư mục `public/`
- Clear browser cache

### Lỗi: AJAX request trả về 404
- Kiểm tra `window.BASE_URL` trong console
- Đảm bảo đang sử dụng `url()` hoặc `window.BASE_URL` trong AJAX

### Lỗi: BASE_URL sai trên production
- Set cụ thể `APP_URL` trong file `.env`
- Kiểm tra cấu hình web server (Apache/Nginx)

## 7. Migration từ version cũ

Nếu bạn đang nâng cấp từ version cũ:

1. Backup dữ liệu và code cũ
2. Update code mới
3. Tạo file `.env` từ `.env.example`
4. Test kỹ các URL và AJAX request
5. Nếu có lỗi, set `APP_URL` cụ thể trong `.env`

## 8. Checklist Triển khai

- [ ] Copy `.env.example` thành `.env`
- [ ] Cấu hình database trong `.env`
- [ ] Import database SQL
- [ ] Set quyền thư mục uploads (755 hoặc 777)
- [ ] Test truy cập trang chủ
- [ ] Test đăng nhập
- [ ] Kiểm tra BASE_URL trong console
- [ ] Test upload file
- [ ] Test các chức năng AJAX

## 9. Yêu cầu hệ thống

- PHP >= 7.4
- MySQL >= 5.7 hoặc MariaDB >= 10.2
- Apache với mod_rewrite hoặc Nginx
- Extension: mysqli, json, mbstring
- Quyền ghi thư mục: public/uploads/, Views/uploads/

## 10. Hỗ trợ

Nếu gặp vấn đề về cấu hình BASE_URL:
1. Kiểm tra file logs
2. Enable DEBUG_MODE trong .env: `APP_DEBUG=true`
3. Kiểm tra PHP error logs
4. Liên hệ admin hệ thống
