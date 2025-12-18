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
    <title>Điều khoản Sử dụng | Taskbb</title>
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
            border-bottom: 3px solid #F59E0B;
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

        .policy-section ul,
        .policy-section ol {
            margin: 15px 0;
            padding-left: 30px;
        }

        .policy-section li {
            margin-bottom: 10px;
        }

        .back-button {
            display: inline-block;
            padding: 12px 24px;
            background: #F59E0B;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: background 0.3s;
        }

        .back-button:hover {
            background: #D97706;
        }

        .highlight-box {
            background: #FEF3C7;
            border-left: 4px solid #F59E0B;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .warning-box {
            background: #FEE2E2;
            border-left: 4px solid #EF4444;
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
            <h1>📜 Điều khoản Sử dụng</h1>
            <p>Cập nhật lần cuối: Tháng 12, 2025</p>
        </div>

        <div class="policy-section">
            <h2>1. Chấp nhận Điều khoản</h2>
            <p>Bằng cách truy cập và sử dụng Taskbb, bạn đồng ý tuân thủ các Điều khoản Sử dụng này. Nếu không đồng ý,
                vui lòng không sử dụng dịch vụ của chúng tôi.</p>
            <div class="highlight-box">
                <strong>Lưu ý quan trọng:</strong> Việc tiếp tục sử dụng dịch vụ sau khi có thay đổi điều khoản đồng
                nghĩa với việc bạn chấp nhận các điều khoản mới.
            </div>
        </div>

        <div class="policy-section">
            <h2>2. Định nghĩa</h2>
            <ul>
                <li><strong>"Dịch vụ":</strong> Nền tảng quản lý công việc Taskbb, bao gồm website, ứng dụng và các tính
                    năng liên quan</li>
                <li><strong>"Người dùng":</strong> Cá nhân hoặc tổ chức đăng ký và sử dụng dịch vụ</li>
                <li><strong>"Nội dung":</strong> Dữ liệu, văn bản, hình ảnh, file được tải lên bởi người dùng</li>
                <li><strong>"Workspace":</strong> Không gian làm việc chung của nhóm/tổ chức</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>3. Đăng ký Tài khoản</h2>
            <h3>3.1. Điều kiện đăng ký</h3>
            <ul>
                <li>Bạn phải từ 16 tuổi trở lên</li>
                <li>Cung cấp thông tin chính xác, đầy đủ và cập nhật</li>
                <li>Chỉ tạo một tài khoản duy nhất (trừ khi có sự cho phép)</li>
                <li>Chịu trách nhiệm về bảo mật tài khoản và mật khẩu</li>
            </ul>

            <h3>3.2. Trách nhiệm của người dùng</h3>
            <ul>
                <li>✅ Giữ bí mật thông tin đăng nhập</li>
                <li>✅ Thông báo ngay nếu phát hiện truy cập trái phép</li>
                <li>✅ Chịu trách nhiệm về mọi hoạt động từ tài khoản của bạn</li>
                <li>❌ KHÔNG chia sẻ tài khoản cho người khác</li>
                <li>❌ KHÔNG sử dụng tài khoản của người khác mà không được phép</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>4. Sử dụng Dịch vụ</h2>
            <h3>4.1. Sử dụng hợp pháp</h3>
            <p>Bạn cam kết sử dụng dịch vụ cho mục đích hợp pháp và không:</p>
            <ul>
                <li>❌ Vi phạm pháp luật Việt Nam hoặc quốc tế</li>
                <li>❌ Xâm phạm quyền sở hữu trí tuệ</li>
                <li>❌ Phát tán virus, malware, hoặc mã độc</li>
                <li>❌ Spam, quấy rối, hoặc lạm dụng người dùng khác</li>
                <li>❌ Thu thập dữ liệu người dùng khác mà không được phép</li>
                <li>❌ Tấn công, hack, hoặc phá hoại hệ thống</li>
                <li>❌ Sử dụng bot, script tự động mà không được phép</li>
            </ul>

            <div class="warning-box">
                <strong>⚠️ Cảnh báo:</strong> Vi phạm các quy định trên có thể dẫn đến đình chỉ hoặc chấm dứt tài khoản
                ngay lập tức mà không cần thông báo trước.
            </div>

            <h3>4.2. Hạn chế sử dụng</h3>
            <ul>
                <li>Tài khoản miễn phí: Giới hạn 5 dự án, 100 nhiệm vụ/tháng, 1GB lưu trữ</li>
                <li>Tài khoản Pro: Không giới hạn dự án, nhiệm vụ, 100GB lưu trữ</li>
                <li>File upload: Tối đa 10MB/file (miễn phí), 100MB/file (Pro)</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>5. Quyền Sở hữu Trí tuệ</h2>
            <h3>5.1. Quyền của Taskbb</h3>
            <p>Taskbb sở hữu toàn bộ quyền đối với:</p>
            <ul>
                <li>Giao diện, thiết kế, logo, tên thương hiệu</li>
                <li>Mã nguồn, cơ sở dữ liệu, thuật toán</li>
                <li>Tài liệu hướng dẫn, nội dung marketing</li>
            </ul>

            <h3>5.2. Quyền của người dùng</h3>
            <p>Bạn giữ quyền sở hữu đối với nội dung mà bạn tạo ra. Bằng cách tải lên, bạn cấp cho Taskbb giấy phép:</p>
            <ul>
                <li>Lưu trữ và hiển thị nội dung của bạn</li>
                <li>Sao lưu và sao chép để đảm bảo an toàn dữ liệu</li>
                <li>Xử lý dữ liệu để cung cấp dịch vụ (ví dụ: tìm kiếm, thông báo)</li>
            </ul>
            <p><em>Chúng tôi KHÔNG sử dụng nội dung của bạn cho mục đích quảng cáo hoặc thương mại.</em></p>
        </div>

        <div class="policy-section">
            <h2>6. Thanh toán và Hoàn tiền</h2>
            <h3>6.1. Gói miễn phí</h3>
            <ul>
                <li>Sử dụng miễn phí trọn đời với tính năng cơ bản</li>
                <li>Không yêu cầu thông tin thẻ tín dụng</li>
            </ul>

            <h3>6.2. Gói trả phí (Pro/Enterprise)</h3>
            <ul>
                <li><strong>Thanh toán:</strong> Hàng tháng hoặc hàng năm (giảm 20%)</li>
                <li><strong>Phương thức:</strong> Thẻ tín dụng/ghi nợ, chuyển khoản ngân hàng</li>
                <li><strong>Gia hạn tự động:</strong> Trừ khi bạn hủy trước kỳ thanh toán</li>
            </ul>

            <h3>6.3. Chính sách hoàn tiền</h3>
            <ul>
                <li>✅ Hoàn tiền 100% trong vòng 30 ngày đầu tiên (không cần lý do)</li>
                <li>✅ Hủy bất kỳ lúc nào - dịch vụ tiếp tục đến hết kỳ đã trả</li>
                <li>❌ Không hoàn tiền cho gói đã sử dụng quá 30 ngày</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>7. Chấm dứt Dịch vụ</h2>
            <h3>7.1. Bởi người dùng</h3>
            <p>Bạn có thể xóa tài khoản bất kỳ lúc nào tại: <strong>Cài đặt → Xóa tài khoản</strong></p>
            <ul>
                <li>Dữ liệu sẽ được lưu trữ 30 ngày (khôi phục nếu cần)</li>
                <li>Sau 30 ngày, dữ liệu bị xóa vĩnh viễn và không thể khôi phục</li>
            </ul>

            <h3>7.2. Bởi Taskbb</h3>
            <p>Chúng tôi có quyền đình chỉ/chấm dứt tài khoản nếu:</p>
            <ul>
                <li>Vi phạm điều khoản sử dụng</li>
                <li>Hoạt động bất thường, nghi ngờ gian lận</li>
                <li>Không thanh toán (đối với gói trả phí)</li>
                <li>Theo yêu cầu của cơ quan chức năng</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>8. Giới hạn Trách nhiệm</h2>
            <p>Taskbb cung cấp dịch vụ "NGUYÊN TRẠNG" (AS IS) và không đảm bảo:</p>
            <ul>
                <li>Dịch vụ hoạt động liên tục, không lỗi (mặc dù chúng tôi cố gắng hết sức)</li>
                <li>Dữ liệu tuyệt đối an toàn (bạn nên tự sao lưu quan trọng)</li>
                <li>Kết quả cụ thể từ việc sử dụng dịch vụ</li>
            </ul>

            <div class="warning-box">
                <strong>Giới hạn bồi thường:</strong> Trong mọi trường hợp, trách nhiệm của chúng tôi không vượt quá số
                tiền bạn đã trả trong 6 tháng gần nhất.
            </div>
        </div>

        <div class="policy-section">
            <h2>9. Bồi thường</h2>
            <p>Bạn đồng ý bồi thường và bảo vệ Taskbb khỏi mọi khiếu nại, tổn thất, thiệt hại phát sinh từ:</p>
            <ul>
                <li>Nội dung bạn đăng tải vi phạm pháp luật hoặc quyền người khác</li>
                <li>Hành vi vi phạm điều khoản sử dụng</li>
                <li>Sử dụng dịch vụ gây thiệt hại cho bên thứ ba</li>
            </ul>
        </div>

        <div class="policy-section">
            <h2>10. Thay đổi Điều khoản</h2>
            <p>Chúng tôi có thể cập nhật điều khoản để phản ánh:</p>
            <ul>
                <li>Thay đổi trong dịch vụ, tính năng mới</li>
                <li>Yêu cầu pháp lý</li>
                <li>Cải thiện quyền lợi người dùng</li>
            </ul>
            <p><strong>Thông báo thay đổi:</strong> Qua email ít nhất 30 ngày trước. Nếu không đồng ý, bạn có thể hủy
                tài khoản.</p>
        </div>

        <div class="policy-section">
            <h2>11. Luật áp dụng và Giải quyết Tranh chấp</h2>
            <p>Điều khoản này được điều chỉnh bởi pháp luật Việt Nam. Mọi tranh chấp sẽ được giải quyết:</p>
            <ol>
                <li><strong>Thương lượng:</strong> Liên hệ support@taskbbf4u.io.vn</li>
                <li><strong>Hòa giải:</strong> Qua trung tâm hòa giải</li>
                <li><strong>Tòa án:</strong> Tòa án có thẩm quyền tại TP. Hồ Chí Minh (phương án cuối cùng)</li>
            </ol>
        </div>

        <div class="policy-section">
            <h2>12. Liên hệ</h2>
            <p>Nếu có thắc mắc về điều khoản:</p>
            <div class="highlight-box">
                <strong>Bộ phận Pháp lý:</strong><br>
                Email: legal@taskbbf4u.io.vn<br>
                Điện thoại: (028) 1234 5678<br>
                Địa chỉ: Tp. Hồ Chí Minh, Việt Nam<br><br>
                <strong>Giờ làm việc:</strong> Thứ 2 - Thứ 6, 9:00 - 18:00 (GMT+7)
            </div>
        </div>

        <div class="policy-section">
            <p style="text-align: center; color: #718096; margin-top: 40px;">
                <em>Bằng cách sử dụng Taskbb, bạn xác nhận đã đọc, hiểu và đồng ý với các Điều khoản Sử dụng này.</em>
            </p>
        </div>

        <a href="<?= url('public/') ?>" class="back-button">← Quay lại Trang chủ</a>
    </div>
</body>

</html>