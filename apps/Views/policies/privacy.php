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
    <title>Chính sách Quyền riêng tư | Taskbb</title>
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
            border-bottom: 3px solid #10B981;
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
            background: #10B981;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #059669;
        }

        .highlight-box {
            background: #D1FAE5;
            border-left: 4px solid #10B981;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .privacy-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .privacy-table th,
        .privacy-table td {
            padding: 12px;
            text-align: left;
            border: 1px solid #e2e8f0;
        }

        .privacy-table th {
            background: #f7fafc;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="policy-container">
        <a href="<?= url('public/') ?>" class="back-button">← Quay lại Trang chủ</a>

        <div class="policy-header">
            <h1>🛡️ Chính sách Quyền riêng tư</h1>
            <p>Cập nhật lần cuối: Tháng 12, 2025</p>
        </div>

        <div class="policy-section">
            <h2>1. Tổng quan</h2>
            <p>Chính sách Quyền riêng tư này mô tả cách Taskbb ("chúng tôi", "của chúng tôi") thu thập, sử dụng, tiết lộ
                và bảo vệ thông tin cá nhân của bạn khi bạn sử dụng nền tảng quản lý công việc Taskbb.</p>
            <div class="highlight-box">
                <strong>Quyền riêng tư của bạn là ưu tiên hàng đầu.</strong> Chúng tôi cam kết minh bạch về việc xử lý
                dữ liệu của bạn.
            </div>
        </div>

        <div class="policy-section">
            <h2>2. Thông tin cá nhân chúng tôi thu thập</h2>

            <table class="privacy-table">
                <thead>
                    <tr>
                        <th>Loại thông tin</th>
                        <th>Mục đích</th>
                        <th>Cơ sở pháp lý</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Họ tên, email</td>
                        <td>Tạo tài khoản, liên hệ</td>
                        <td>Thỏa thuận sử dụng</td>
                    </tr>
                    <tr>
                        <td>Mật khẩu (đã mã hóa)</td>
                        <td>Xác thực, bảo mật</td>
                        <td>Lợi ích hợp pháp</td>
                    </tr>
                    <tr>
                        <td>Địa chỉ IP, log truy cập</td>
                        <td>Bảo mật, phòng chống gian lận</td>
                        <td>Lợi ích hợp pháp</td>
                    </tr>
                    <tr>
                        <td>Dữ liệu công việc (task, project)</td>
                        <td>Cung cấp dịch vụ</td>
                        <td>Thỏa thuận sử dụng</td>
                    </tr>
                    <tr>
                        <td>Cookie, session</td>
                        <td>Duy trì phiên đăng nhập</td>
                        <td>Sự đồng ý</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="policy-section">
            <h2>3. Cách chúng tôi sử dụng thông tin</h2>
            <h3>3.1. Mục đích sử dụng chính</h3>
            <ul>
                <li>✅ Cung cấp và vận hành dịch vụ Taskbb</li>
                <li>✅ Quản lý tài khoản và xác thực người dùng</li>
                <li>✅ Giao tiếp với bạn về dịch vụ, hỗ trợ kỹ thuật</li>
                <li>✅ Cải thiện tính năng dựa trên phản hồi</li>
                <li>✅ Bảo mật hệ thống và phòng chống gian lận</li>
            </ul>

            <h3>3.2. Mục đích sử dụng phụ (với sự đồng ý)</h3>
            <ul>
                <li>📧 Gửi thông tin sản phẩm, tính năng mới</li>
                <li>📊 Phân tích hành vi người dùng để tối ưu UX</li>
                <li>🎯 Cá nhân hóa trải nghiệm</li>
            </ul>
            <p><em>Bạn có thể từ chối các mục đích phụ bất kỳ lúc nào trong Cài đặt tài khoản.</em></p>
        </div>

        <div class="policy-section">
            <h2>4. Chia sẻ thông tin</h2>
            <h3>4.1. Chúng tôi KHÔNG chia sẻ với:</h3>
            <ul>
                <li>❌ Công ty quảng cáo</li>
                <li>❌ Broker dữ liệu</li>
                <li>❌ Bên thứ ba vì mục đích thương mại</li>
            </ul>

            <h3>4.2. Chúng tôi CHỈ chia sẻ với:</h3>
            <ul>
                <li><strong>Nhà cung cấp dịch vụ:</strong> Hosting (AWS/DigitalOcean), email (SendGrid), thanh toán
                    (Stripe) - có thỏa thuận bảo mật</li>
                <li><strong>Cơ quan chức năng:</strong> Khi có yêu cầu pháp lý hợp lệ</li>
                <li><strong>Trong tổ chức của bạn:</strong> Nếu bạn là thành viên của workspace doanh nghiệp</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>5. Quyền của bạn theo GDPR & Luật Việt Nam</h2>
            <h3>Bạn có các quyền sau:</h3>
            <ul>
                <li><strong>🔍 Quyền truy cập:</strong> Xem thông tin cá nhân chúng tôi lưu trữ</li>
                <li><strong>✏️ Quyền sửa đổi:</strong> Cập nhật thông tin không chính xác</li>
                <li><strong>🗑️ Quyền xóa:</strong> Yêu cầu xóa dữ liệu (trừ khi có nghĩa vụ pháp lý)</li>
                <li><strong>📤 Quyền truy xuất:</strong> Tải xuống dữ liệu của bạn (định dạng JSON/CSV)</li>
                <li><strong>⛔ Quyền hạn chế:</strong> Giới hạn cách chúng tôi xử lý dữ liệu</li>
                <li><strong>🚫 Quyền phản đối:</strong> Phản đối việc xử lý dữ liệu cho mục đích marketing</li>
                <li><strong>📧 Quyền rút lại đồng ý:</strong> Hủy đồng ý trước đó bất kỳ lúc nào</li>
            </ul>

            <div class="highlight-box">
                <strong>Cách thực hiện quyền:</strong><br>
                • Truy cập: Cài đặt → Quyền riêng tư → Tải xuống dữ liệu<br>
                • Xóa: Cài đặt → Xóa tài khoản<br>
                • Liên hệ: privacy@taskbbf4u.io.vn
            </div>
        </div>

        <div class="policy-section">
            <h2>6. Bảo vệ dữ liệu của trẻ em</h2>
            <p>Taskbb không dành cho người dưới 16 tuổi. Nếu bạn là phụ huynh và phát hiện con bạn sử dụng dịch vụ mà
                không có sự cho phép, vui lòng liên hệ ngay để chúng tôi xóa tài khoản.</p>
        </div>

        <div class="policy-section">
            <h2>7. Chuyển giao dữ liệu quốc tế</h2>
            <p>Dữ liệu của bạn được lưu trữ tại Việt Nam. Nếu bạn truy cập từ nước ngoài, vui lòng lưu ý rằng dữ liệu sẽ
                được chuyển và xử lý tại Việt Nam theo luật địa phương.</p>
        </div>

        <div class="policy-section">
            <h2>8. Thời gian lưu trữ</h2>
            <table class="privacy-table">
                <thead>
                    <tr>
                        <th>Loại dữ liệu</th>
                        <th>Thời gian lưu trữ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Thông tin tài khoản</td>
                        <td>Trong suốt thời gian hoạt động + 30 ngày</td>
                    </tr>
                    <tr>
                        <td>Dữ liệu công việc</td>
                        <td>Trong suốt thời gian hoạt động + 30 ngày</td>
                    </tr>
                    <tr>
                        <td>Log truy cập</td>
                        <td>90 ngày</td>
                    </tr>
                    <tr>
                        <td>Dữ liệu thanh toán</td>
                        <td>5 năm (theo quy định pháp luật)</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="policy-section">
            <h2>9. Cookie và công nghệ tương tự</h2>
            <p>Chúng tôi sử dụng các loại cookie sau:</p>
            <ul>
                <li><strong>Cookie thiết yếu:</strong> Duy trì phiên đăng nhập (không thể tắt)</li>
                <li><strong>Cookie chức năng:</strong> Ghi nhớ tùy chọn giao diện, ngôn ngữ</li>
                <li><strong>Cookie phân tích:</strong> Google Analytics (có thể tắt)</li>
            </ul>
            <p>Bạn có thể quản lý cookie trong: <strong>Cài đặt → Quyền riêng tư → Quản lý Cookie</strong></p>
        </div>

        <div class="policy-section">
            <h2>10. Cập nhật chính sách</h2>
            <p>Chúng tôi có thể cập nhật chính sách này. Thay đổi quan trọng sẽ được thông báo qua:</p>
            <ul>
                <li>📧 Email (ít nhất 30 ngày trước khi có hiệu lực)</li>
                <li>🔔 Thông báo trong ứng dụng</li>
                <li>📄 Banner trên trang web</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>11. Liên hệ</h2>
            <p>Nếu có thắc mắc về quyền riêng tư:</p>
            <div class="highlight-box">
                <strong>Nhân viên Bảo vệ Dữ liệu (DPO):</strong><br>
                Email: privacy@taskbbf4u.io.vn<br>
                Điện thoại: (028) 1234 5678<br>
                Địa chỉ: Tp. Hồ Chí Minh, Việt Nam<br><br>
                <strong>Thời gian phản hồi:</strong> Trong vòng 7 ngày làm việc
            </div>
        </div>

        <a href="<?= url('public/') ?>" class="back-button">← Quay lại Trang chủ</a>
    </div>
</body>

</html>