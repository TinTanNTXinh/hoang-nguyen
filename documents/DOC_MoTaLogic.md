# MÔ TẢ LOGIC 
# DỰ ÁN QUẢN LÝ VẬN TẢI - HOÀNG NGUYỄN

## 1. Người dùng - User:

- Một người dùng có nhiều chức vụ.
- Phân quyền danh mục (chức năng).
- Phân quyền chi tiết

## 2. Xe - Truck:

- Trạng thái: Do người quản lý tự điều chỉnh. Gồm:
  + Chưa phân tài
  + Đang giao hàng
  + Đã giao hàng
  + Không giao được.

- Có thể xem lịch sử phân tài.
- Xe dù không có tài xế, vẫn có thể sử dụng để tạo đơn hàng, chi phí như bình thường.

## 3. Nhà xe – Garage:

- Gồm: Nhà xe công ty, Nhà xe ngoài

## 4. Khách hàng - Customer:
- Gồm 2 loại: Công ty, Cá nhân.
- Có thể thêm nhân viên cho khách hàng.
- Số phần trăm để tạo cước phí mới khi giá dầu thay đổi (cột limit_oil). 
  + Hoặc có thể hiểu như "Số phần trăm khi giá dầu đạt mức này, sẽ tạo cước phí mới"
  + (Ví dụ là 10%: Giá dầu hiện tại 10k, khi giá dầu tăng lên 11k tức là 10% thì tạo công thức mới)
  + (Cột này mục đích để xác định khi nào tạo cước phí mới. Không dùng để tính cước phí mới)

- Số phần trăm giá dầu/cước phí (cột oil_per_postage):
  + (Cột này mục đích để tính cước phí mới)
  + Công thức: 
```
    Cước phí mới = Cước phí cũ 
                    + (Cước phí cũ 
                    * abs(Số phần trăm tăng hoặc giảm giá dầu lần này so với lần trước) 
                    * Số phần trăm nhiên liệu/cước phí trong bảng khách hàng 
                    / 10000)
```

- Ngày áp dụng (cột apply_date):
  + Nếu gía dầu thay đổi phù hợp để tạo cước phí mới, và ngày tạo giá dầu nằm sau ngày áp dụng thì mới được tạo cước phí mới
  + Tóm lại: Điều kiện để tạo cước phí mới là:
```
    % quy định trong Khách hàng <= hoặc >= Số phần trăm tăng hoặc giảm giá dầu lần này so với lần trước
    Ngày tạo giá dầu nằm sau ngày áp dụng trong bảng khách hàng
```

## 5. Cước phí - Postage:

- Nếu công thức có giá dầu thì khi gía dầu tăng hoặc giảm cũng không tạo cước phí mới
- Mỗi cước phí đều có ngày áp dụng.
- 2 cước phí có công thức giống nhau nhưng khác ngày áp dụng vẫn là 2 cước phí.
- % Phí giao xe: Số tiền phải trả cho nhà xe
    (Ví dụ là 10%: Doanh thu chuyến đó 3tr thì trả cho nhà xe 300k)
- Hiển thị giá dầu hiện tại (mới nhất) trên form này.

## 6. Chi phí - Cost:
- Số tiền mà xe này phát sinh trong khoảng thời gian nào đó.
- Đậu bãi:
  + Định giá đậu bãi cho từng loại xe. (3h là 1 ngày, hơn 3h là 2 ngày)
  + Mặc định:
```
        Xe 5 tấn = 30k/1 ngày
        Xe 8 tấn = 40k/1 ngày
        Xe container = 50k/1 ngày
```
  + Chi phí đậu bãi = Số ngày đậu * đơn giá.

- Dầu, nhớt: Mặc định giảm 3%, Ví dụ: 10k/1 lít còn 9,727k/1 lít.

## 7. Đơn hàng- Transport:
- Doanh thu = (Số lượng hàng * Đơn giá) + (Bốc xếp, Neo đêm, Công an, Đậu bãi, Thêm điểm)
- Nếu Đơn vị tính là Vnd/Chuyến:
  + Doanh thu = Đơn giá + (Bốc xếp, Neo đêm, Công an, Đậu bãi, Thêm điểm)

- Giao xe = % Giao xe * (Số lượng hàng * Đơn giá(K bat buoc neu la Vnd/Chuyến)) / 100
  ```hoặc```
- Giao xe = % Giao xe * (Doanh thu - (Bốc xếp, Neo đêm, Công an, Đậu bãi, Thêm điểm)) / 100

- Dùng Các điều kiện + Ngày vận chuyển để tìm công thức phù hợp.
- Nếu công thức có giá dầu lấy giá dầu theo ngày vận chuyển load lên
- Đơn hàng khống: cho phép tạo rỗng đơn hàng.
- Nhận: là tiền tài xế nhận của Khách hàng (nếu khách hàng muốn trả trước)
- Doanh thu = SL * Đơn giá + (Bốc xếp, Neo đêm, Công an, Đậu bãi, Thêm điểm)
- Nếu công thức có tuyến đường: Ẩn Nơi nhận và nơi giao

## 8. Công nợ Khách hàng - Invoice & InvoiceDetail:
- Đổi màu những đơn hàng đã xuất hóa đơn nhưng chưa xuất hết tiền.
- Các đơn hàng được chọn phải cùng khách hàng.
- Tiền xuất hóa đơn, PTT = Tổng doanh thu của các đơn hàng đã chọn.
    ```(Riêng hóa đơn có thể điều chỉnh tiền muốn xuất)```
- Cho phép tạo 2 loại là: Hóa đơn và Phiếu thanh toán.
    ```(Hóa đơn có VAT, Phiếu thanh toán không VAT)```
- Nếu các đơn hàng có tiền nhận. Đưa tổng tiền nhận này trả vào Hóa đơn hoặc PTT luôn.
- Hóa đơn được xuất nhiều lần, PTT xuất 1 lần.
- Cả hóa đơn và PTT để được trả nhiều lần.
- Người nhận là nhân viên của Khách hàng, chỉ lưu text.
- Mã lấy tự động.
- Thêm cột ngày thanh toán, để nhắc nhớ thanh toán khi đến hẹn. (Nếu hóa đơn, PTT chưa trả đủ)
- Đã tạo hóa đơn thì k được tạo PTT và ngược lại.

## 9. Công nợ xe - Invoice & InvoiceDetail:
- Các đơn hàng được chọn phải cùng số xe.
- Xuất 1 lần.
- Được trả nhiều lần.
```
Tiền xuất PTT = Tổng tiền giao xe của các đơn hàng 
            + Tổng Thực tế sau khi giao về(Bốc xếp_real, Neo đêm_real, Công an_real, Phí tăng bo_real, Thêm điểm_real) của các đơn hàng 
            - Tổng (Dầu, Thay nhớt, Đậu bãi, khác) của xe này.
```

## Ghi chú
- NGÀY ÁP DỤNG, GIỜ ÁP DỤNG: Có ở các bảng:
  + Khách hàng.
  + Giá dầu, Giá nhớt.
  + Cước phí.

-> Được so sánh với bảng Transport cột transport_date.












