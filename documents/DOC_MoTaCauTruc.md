# MÔ TẢ CẤU TRÚC
# DỰ ÁN QUẢN LÝ VẬN TẢI - HOÀNG NGUYỄN

## 1. Giới thiệu
- Dự án được phát triển dựa trên công nghệ Laravel 5.4 và Angular 4
- Các Design Pattern sử dụng trong dự án:
  + Laravel: Json Web Token (JWT), Repository, Dependency Injection (Service Container được Laravel cung cấp sẵn)
  + Angular: Observer

## 2. Cấu trúc phía server Laravel
- Dựa trên cấu trúc có sẵn của framework và phát triển thêm 3 tầng là: Common, Repository và Service

- Common: Gồm:
  + Helpers: Chứa các class tĩnh, phục vụ cho các việc xử lý tiền, ngày giờ, chuỗi query, lọc dữ liệu, mã hóa, trạng thái response.
  + Interfaces: Gồm các Interface cho Controller, tránh việc không đồng nhất tên hàm.
  + Traits: (chưa sử dụng) thực hiện kế thừa trong PHP

- Repositories:
  + Chịu trách nhiệm tương tác trực tiếp với Model với sự hỗ trợ của Eloquent (Eloquent được Laravel cung cấp sẵn cho việc query)
  + Chứa các interface và các class implement
  + Đã viết sẵn CRUD trong lớp base
  + Các tầng khác (thường là tầng Services) tương tác với tầng Repositories thông qua interface để giữ được nguyên lý Dependency Injection

- Services: (Tầng xử lý logic)
  + Trách nhiệm: Xử lý logic (nghiệp vụ) như validate dữ liệu, CRUD bảng cha và bảng con, ... 
  + Gọi các hàm trong tầng Repositories thông qua interface để phục vụ cho xử lý logic.
  + Controller chỉ được phép sử dụng tầng Services để xử lý dữ liệu trong request.
  + Chứa các interface và các class implement.
  + Các tầng khác (thường là Controller) tương tác với tầng Services thông qua interface để giữ được nguyên lý Dependency Injection.
  + Chú ý: có sử dụng interface extends interface.

- Class AppServiceProvider chỉ dẫn các denpendency trong hàm register().
- Các tầng chỉ giao tiếp với nhau thông qua interface, không giao tiếp qua class implement.

## 3. Cấu trúc phía client Angular:
- Dùng Observer để kiểm tra đăng nhập.
- Source code chứa trong public/dev, gồm:

  + components                : Chứa các thành phần chính của dự án, nơi xử lý UI và Business logic
  + dynamic-components        : Chứa các control UI tạo sẵn, chỉ việc sử dụng như: Autocomplete, Table, Table master detail, ...
  + interface-components      : Chứa các interface để tên hàm được thống nhất trong lớp components.
  + layout-components         : Chứa UI chính của web app như header, footer, sidebar, login, ...
  + middlewares               : (Chưa sử dụng) Validate trước khi vào route
  + models                    : (Chưa sử dụng) Kiểu dữ liệu cho object
  + pipes                     : Tùy chỉnh kiểu hiện thị dữ liệu khi hiển thị trong HTML
  + services                  : Xử lý Http, Authentication và Chứa các class helpers giúp xử lý dữ liệu mảng, tiền, ngày giờ, tập tin, ...

## 4. Work flow Client-Server:
```
Client      ->      Routes      ->      Controller      ->      Service     ->      Repository       ->      Model       ->      Database 
(Angular)           (api.php)                                                                                       (Eloquent)   (MySQL)       
```