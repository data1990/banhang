# Hướng dẫn cấu hình VietQR

## Tổng quan

Tính năng tích hợp VietQR để tạo mã QR thanh toán tự động khi khách hàng chọn phương thức thanh toán chuyển khoản ngân hàng.

## Cấu hình

### 1. Cập nhật file `.env`

Thêm các cấu hình sau vào file `.env`:

```env
VIETQR_API_URL=https://img.vietqr.io/image
```

Lưu ý: API của VietQR miễn phí và không cần API key.

### 2. Cấu hình thông tin ngân hàng trong Settings

Truy cập trang quản trị (Admin Panel) và cấu hình các settings sau:

#### Trong bảng `settings`, thêm các key sau:

| Key | Ví dụ giá trị | Mô tả |
|-----|---------------|-------|
| `bank.code` | `VCB` | Mã ngân hàng (xem danh sách bên dưới) |
| `bank.account_number` | `1234567890` | Số tài khoản ngân hàng |
| `bank.transfer_info` | HTML content | Thông tin chuyển khoản (HTML) |

### 3. Danh sách mã ngân hàng

| Ngân hàng | Mã |
|-----------|-----|
| Vietcombank | VCB |
| Techcombank | TCB |
| VPBank | VPB |
| MB Bank | MBB |
| TPBank | TPB |
| ACB | ACB |
| VietinBank | CTG |
| BIDV | BIDV |
| Agribank | VBA |
| Sacombank | STB |
| SHB | SHB |
| VIB | VIB |
| Eximbank | EIB |
| MSB | MSB |
| NCB | NCB |
| SeABank | SEAB |
| PVcomBank | PVB |
| HDBank | HDB |
| CAKE by VPBank | VPB |
| Ubank | Ubank |
| ... | ... |

### 4. Ví dụ cấu hình Settings

Chạy các lệnh SQL sau hoặc thêm qua giao diện admin:

```sql
INSERT INTO settings (`key`, `value`, `type`, `group`) VALUES
('bank.code', 'VCB', 'text', 'bank'),
('bank.account_number', '1234567890', 'text', 'bank'),
('bank.transfer_info', '<p>Số tài khoản: <strong>1234567890</strong><br>Chủ tài khoản: <strong>NGUYEN VAN A</strong><br>Ngân hàng: <strong>VIETCOMBANK</strong></p>', 'text', 'bank');
```

### 5. Cách hoạt động

1. Khách hàng chọn phương thức thanh toán "Chuyển khoản ngân hàng"
2. Khách hàng hoàn tất đặt hàng
3. Hệ thống tự động tạo mã QR theo thông tin:
   - Mã ngân hàng (bank.code)
   - Số tài khoản (bank.account_number)
   - Số tiền (grand_total của đơn hàng)
   - Nội dung: "DH000001" (mã đơn hàng định dạng 6 số)
4. Mã QR được hiển thị trên trang thành công đặt hàng
5. Khách hàng quét mã QR bằng app ngân hàng để thanh toán

### 6. Format URL QR Code

```
https://img.vietqr.io/image/{bankCode}-{accountNo}-{template}-{amount}-{addInfo}.png

Ví dụ:
https://img.vietqr.io/image/VCB-1234567890-compact2-1000000-DH000001.png
```

Trong đó:
- `bankCode`: Mã ngân hàng (ví dụ: VCB)
- `accountNo`: Số tài khoản
- `template`: Template mã QR (compact, compact2, print)
- `amount`: Số tiền (không có dấu phẩy hoặc chấm)
- `addInfo`: Nội dung thanh toán (tối đa 25 ký tự, không có ký tự đặc biệt)

### 7. Lưu ý

- Nội dung QR code sẽ tự động được làm sạch (loại bỏ dấu, ký tự đặc biệt)
- Giới hạn 25 ký tự cho nội dung
- Hỗ trợ tiếng Việt (sẽ được chuyển thành không dấu)
- QR code được tạo trực tiếp từ URL, không cần API key

### 8. Test

Để test tính năng:

1. Đảm bảo đã cấu hình bank.code và bank.account_number
2. Đặt hàng với phương thức chuyển khoản
3. Kiểm tra xem QR code có hiển thị trên trang success không
4. Quét QR code bằng app ngân hàng để xác nhận

## Troubleshooting

### QR code không hiển thị

1. Kiểm tra cấu hình `bank.code` và `bank.account_number` trong settings
2. Kiểm tra console trình duyệt xem có lỗi loading image không
3. Đảm bảo URL QR code hợp lệ (không có ký tự đặc biệt)

### QR code không quét được

1. Kiểm tra số tài khoản có đúng không
2. Kiểm tra mã ngân hàng có đúng không
3. Xem format URL QR code có đúng không


