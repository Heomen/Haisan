# 🦞 Hệ thống Website & Quản lý Nhà hàng Hải sản SeaFood

Chào mừng đến với dự án **SeaFood** - Hệ thống Website giới thiệu, đặt bàn trực tuyến tích hợp cổng quản trị nội bộ dành cho chuỗi nhà hàng hải sản cao cấp. Dự án được phát triển trên kiến trúc MVC PHP thuần, sử dụng cơ sở dữ liệu MySQL, giao diện responsive hiện đại và tích hợp công cụ hỗ trợ thông minh.

---

## 🌟 Các tính năng chính

### 1. Website Khách hàng (Frontend)
- **Trang chủ & Giới thiệu:** Trải nghiệm thiết kế cao cấp với các hiệu ứng mượt mà, giới thiệu văn hóa và cam kết chất lượng của chuỗi nhà hàng.
- **Thực đơn động:** Hiển thị thực đơn và các món chế biến được cấu hình trực tiếp từ cơ sở dữ liệu.
- **Đặt bàn trực tuyến:** Khách hàng có thể dễ dàng điền thông tin đặt chỗ, chọn cơ sở ăn uống, số lượng khách, thời gian và chọn trước các món hải sản/món chế biến.
- **Đánh giá & Phản hồi:** Khách hàng đã đăng nhập có thể gửi nhận xét và chấm điểm dịch vụ.
- **Trợ lý ảo SeaFood AI:** Chatbot tư vấn chạy cục bộ thông minh, hỗ trợ giải đáp nhanh về địa chỉ các cơ sở, chương trình khuyến mãi hiện hành, thông tin giá cả thực đơn, hướng dẫn đặt bàn và tự động lọc các câu hỏi ngoài phạm vi website.

### 2. Trang Quản trị & Nhân viên (Backend)
- **Bảng điều khiển (Dashboard):** Thống kê nhanh số lượng bàn đặt mới, tổng số món ăn trong menu và số lượng đánh giá chờ duyệt.
- **Quản lý Đặt bàn (Reservations):** 
  - Theo dõi danh sách chi tiết các bàn đặt.
  - Cập nhật trạng thái bàn đặt (*Chờ xử lý, Đã xác nhận, Đã hoàn thành, Đã hủy*).
  - Tự động hoàn trả số lượng hải sản vào kho khi đơn đặt bàn bị hủy hoặc thay đổi số lượng.
- **Quản lý Thực đơn (Menu):**
  - Thêm, sửa, xóa các loại hải sản tươi sống và món chế biến đi kèm.
  - Quản lý tồn kho số lượng sản phẩm, tự động chuyển trạng thái sang *"Ngừng phục vụ"* khi hết hàng và tự động mở lại khi nhập kho.
  - Cấu hình hiển thị *"Món ưa chuộng"* trên trang chủ.
- **Quản lý Đánh giá (Reviews):** Kiểm duyệt (duyệt/ẩn) các đánh giá của khách hàng trước khi hiển thị công khai trên trang chủ.
- **Quản lý Nhân viên (Employees):** Thêm mới và quản trị tài khoản nhân viên hệ thống.

---

## 📂 Cấu trúc thư mục dự án

Dự án tuân thủ mô hình thiết kế **MVC (Model-View-Controller)**:

```text
Haisan/
│
├── config/              # Cấu hình kết nối cơ sở dữ liệu (database.php)
├── controllers/         # Các Controller xử lý logic điều hướng và nghiệp vụ
│   ├── HomeController.php
│   ├── MenuController.php
│   ├── ReservationController.php
│   └── ...
├── models/              # Các Model tương tác trực tiếp với Database
│   ├── Menu.php
│   ├── Reservation.php
│   └── ...
├── views/               # Giao diện HTML/CSS/JS (chia theo Module)
│   ├── home/            # Giao diện trang chủ khách hàng
│   ├── layout/          # Các thành phần chung (header, footer, sidebar...)
│   ├── menu/            # Trang quản lý thực đơn phía admin
│   └── ...
├── uploads/             # Thư mục lưu trữ hình ảnh tải lên của món ăn
├── index.php            # File định tuyến (Routing) chính của ứng dụng
├── database.sql         # File lược đồ cơ sở dữ liệu MySQL
└── README.md            # Tài liệu hướng dẫn sử dụng dự án
```

---

## 🛠 Hướng dẫn Cài đặt & Cấu hình

### 1. Yêu cầu hệ thống
- Máy chủ Web: Apache (khuyên dùng **XAMPP**).
- Phiên bản PHP: **7.4** trở lên (hỗ trợ cURL extension, PDO extension).
- Cơ sở dữ liệu: **MySQL / MariaDB**.

### 2. Các bước triển khai
1. **Tải mã nguồn:** Sao chép thư mục dự án `Haisan` vào thư mục chạy web của bạn (ví dụ: `C:\xampp\htdocs\` trên Windows).
2. **Khởi tạo Cơ sở dữ liệu:**
   - Mở công cụ quản lý MySQL (ví dụ: `phpMyAdmin` tại địa chỉ `http://localhost/phpmyadmin/`).
   - Tạo một cơ sở dữ liệu mới tên là `haisan` với định dạng mã hóa `utf8mb4_unicode_ci`.
   - Nhập (Import) tệp tin `database.sql` có sẵn trong thư mục dự án vào cơ sở dữ liệu vừa tạo.
3. **Cấu hình Kết nối Database:**
   - Mở tệp tin `config/database.php` và cập nhật các thông số kết nối phù hợp với máy chủ của bạn (Host, DB Name, Username, Password).
4. **Truy cập ứng dụng:**
   - Mở trình duyệt web và truy cập địa chỉ: `http://localhost/Haisan/` để trải nghiệm giao diện khách hàng.
   - Để truy cập trang quản trị nội bộ: truy cập `http://localhost/Haisan/index.php?controller=auth&action=login`.

---

## 🔐 Tài khoản Đăng nhập Hệ thống

Để truy cập vào hệ thống quản lý nội bộ (Admin/Nhân viên), sử dụng tài khoản mẫu:
- **Tài khoản:** `admin` (hoặc email quản trị của bạn)
- **Mật khẩu:** mật khẩu đã đăng ký trong cơ sở dữ liệu (tham khảo tệp `create_admin.php` để tạo mới nhanh tài khoản quản trị).

---
*Dự án được xây dựng với mục tiêu đem lại trải nghiệm vận hành tối ưu, trơn tru cho chuỗi nhà hàng SeaFood.*
