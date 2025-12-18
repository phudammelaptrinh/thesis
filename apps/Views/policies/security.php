<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chính sách Bảo mật | Taskbb</title>
    <link rel="icon" type="image/png" href="<?= asset('logo/logo.png') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .policy-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .policy-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 3px solid #4F46E5;
        }

        .policy-header h1 {
            color: #1a202c;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .policy-header p {
            color: #718096;
            font-size: 1rem;
        }

        .policy-section {
            margin-bottom: 30px;
        }

        .policy-section h2 {
            color: #2d3748;
            font-size: 1.5rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
        }

        .policy-section h3 {
            color: #4a5568;
            font-size: 1.2rem;
            margin: 20px 0 10px;
        }

        .policy-section p {
            margin-bottom: 15px;
            text-align: justify;
        }

        .policy-section ul {
            margin: 15px 0;
            padding-left: 30px;
        }

        .policy-section li {
            margin-bottom: 10px;
        }

        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background: #4F46E5;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #4338CA;
        }

        .highlight-box {
            background: #EEF2FF;
            border-left: 4px solid #4F46E5;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="policy-container">
        <a href="<?= url('public/') ?>" class="back-button">← Quay lại Trang chủ</a>

        <div class="policy-header">
            <h1>🔒 Chính sách Bảo mật</h1>
            <p>Cập nhật lần cuối: Tháng 12, 2025</p>
        </div>

        <div class="policy-section">
            <h2>1. Giới thiệu</h2>
            <p>Taskbb cam kết bảo vệ quyền riêng tư và thông tin cá nhân của người dùng. Chính sách bảo mật này mô tả
                cách chúng tôi thu thập, sử dụng, lưu trữ và bảo vệ thông tin của bạn khi sử dụng dịch vụ của chúng tôi.
            </p>
            <div class="highlight-box">
                <strong>Cam kết của chúng tôi:</strong> Chúng tôi không bao giờ bán hoặc chia sẻ thông tin cá nhân của
                bạn với bên thứ ba vì mục đích thương mại.
            </div>
        </div>

        <div class="policy-section">
            <h2>2. Thông tin chúng tôi thu thập</h2>
            <h3>2.1. Thông tin bạn cung cấp</h3>
            <ul>
                <li><strong>Thông tin tài khoản:</strong> Họ tên, địa chỉ email, mật khẩu (được mã hóa)</li>
                <li><strong>Thông tin hồ sơ:</strong> Ảnh đại diện, vai trò công việc, thông tin liên hệ</li>
                <li><strong>Nội dung công việc:</strong> Dự án, nhiệm vụ, báo cáo, tài liệu đính kèm</li>
                <li><strong>Thông tin thanh toán:</strong> (Nếu sử dụng gói trả phí) thông tin thẻ tín dụng được xử lý
                    qua cổng thanh toán bảo mật</li>
            </ul>

            <h3>2.2. Thông tin tự động thu thập</h3>
            <ul>
                <li><strong>Log truy cập:</strong> Địa chỉ IP, loại trình duyệt, thời gian truy cập</li>
                <li><strong>Cookie:</strong> Để duy trì phiên đăng nhập và cải thiện trải nghiệm người dùng</li>
                <li><strong>Dữ liệu sử dụng:</strong> Tính năng được sử dụng, thời gian sử dụng, hành vi tương tác</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>3. Cách chúng tôi sử dụng thông tin</h2>
            <p>Chúng tôi sử dụng thông tin của bạn để:</p>
            <ul>
                <li>Cung cấp và duy trì dịch vụ Taskbb</li>
                <li>Xác thực danh tính và bảo mật tài khoản</li>
                <li>Cải thiện và phát triển tính năng mới</li>
                <li>Gửi thông báo về cập nhật, bảo trì hệ thống</li>
                <li>Phân tích xu hướng sử dụng để tối ưu hóa dịch vụ</li>
                <li>Tuân thủ các yêu cầu pháp lý và ngăn chặn gian lận</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>4. Bảo mật thông tin</h2>
            <h3>4.1. Các biện pháp bảo mật</h3>
            <ul>
                <li><strong>Mã hóa dữ liệu:</strong> HTTPS/TLS cho tất cả kết nối</li>
                <li><strong>Mã hóa mật khẩu:</strong> Sử dụng bcrypt với salt ngẫu nhiên</li>
                <li><strong>Xác thực 2 lớp:</strong> (Tùy chọn) OTP qua email</li>
                <li><strong>Firewall:</strong> Bảo vệ server khỏi tấn công</li>
                <li><strong>Sao lưu định kỳ:</strong> Backup dữ liệu hàng ngày</li>
                <li><strong>Giới hạn truy cập:</strong> Chỉ nhân viên được ủy quyền mới truy cập dữ liệu</li>
            </ul>

            <h3>4.2. Quyền của bạn</h3>
            <p>Bạn có quyền:</p>
            <ul>
                <li>Truy cập và tải xuống dữ liệu cá nhân</li>
                <li>Chỉnh sửa hoặc cập nhật thông tin</li>
                <li>Yêu cầu xóa tài khoản và dữ liệu</li>
                <li>Từ chối nhận email marketing</li>
                <li>Khiếu nại về việc xử lý dữ liệu</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>5. Chia sẻ thông tin với bên thứ ba</h2>
            <p>Chúng tôi chỉ chia sẻ thông tin trong các trường hợp sau:</p>
            <ul>
                <li><strong>Nhà cung cấp dịch vụ:</strong> Hosting, email, thanh toán (có thỏa thuận bảo mật)</li>
                <li><strong>Yêu cầu pháp lý:</strong> Khi được cơ quan chức năng yêu cầu</li>
                <li><strong>Với sự đồng ý của bạn:</strong> Khi bạn cho phép rõ ràng</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>6. Lưu trữ dữ liệu</h2>
            <p>Dữ liệu của bạn được lưu trữ tại:</p>
            <ul>
                <li>Server tại Việt Nam (data center đạt chuẩn ISO 27001)</li>
                <li>Thời gian lưu trữ: Trong suốt thời gian tài khoản hoạt động + 30 ngày sau khi xóa</li>
                <li>Sau khi xóa tài khoản, dữ liệu sẽ được xóa vĩnh viễn sau 30 ngày</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>7. Cookie và công nghệ theo dõi</h2>
            <p>Chúng tôi sử dụng cookie để:</p>
            <ul>
                <li>Duy trì phiên đăng nhập</li>
                <li>Ghi nhớ tùy chọn ngôn ngữ và giao diện</li>
                <li>Phân tích lưu lượng truy cập (Google Analytics)</li>
            </ul>
            <p>Bạn có thể tắt cookie trong trình duyệt, nhưng một số tính năng có thể không hoạt động.</p>
        </div>

        <div class="policy-section">
            <h2>8. Quyền riêng tư của trẻ em</h2>
            <p>Taskbb không dành cho người dưới 16 tuổi. Chúng tôi không cố ý thu thập thông tin từ trẻ em. Nếu phát
                hiện, chúng tôi sẽ xóa ngay lập tức.</p>
        </div>

        <div class="policy-section">
            <h2>9. Thay đổi chính sách</h2>
            <p>Chúng tôi có thể cập nhật chính sách này. Thay đổi quan trọng sẽ được thông báo qua email hoặc trên trang
                web.</p>
        </div>

        <div class="policy-section">
            <h2>10. Liên hệ</h2>
            <p>Nếu có thắc mắc về chính sách bảo mật, vui lòng liên hệ:</p>
            <div class="highlight-box">
                <strong>Email:</strong> privacy@taskbbf4u.io.vn<br>
                <strong>Điện thoại:</strong> (028) 1234 5678<br>
                <strong>Địa chỉ:</strong> Tp. Hồ Chí Minh, Việt Nam
            </div>
        </div>

        <a href="<?= url('public/') ?>" class="back-button">← Quay lại Trang chủ</a>
    </div>
</body>

</html>