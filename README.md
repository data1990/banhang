# BanHang - Laravel E-Commerce System

Hệ thống thương mại điện tử được xây dựng bằng Laravel 11 với Bootstrap 5, hỗ trợ thanh toán VND và tích hợp Zalo/Messenger.

## Tính năng chính

### Frontend
- Trang chủ với sản phẩm mới và bán chạy
- Danh sách sản phẩm với bộ lọc và tìm kiếm
- Chi tiết sản phẩm với gallery ảnh
- Giỏ hàng (hỗ trợ guest và user)
- Checkout với thông tin khách hàng
- Đăng nhập/đăng ký + Google OAuth
- Khu vực tài khoản khách hàng

### Admin Panel
- Dashboard với thống kê doanh thu
- Quản lý sản phẩm (CRUD + upload ảnh)
- Quản lý đơn hàng với thay đổi trạng thái
- Cấu hình tích hợp Zalo/Messenger
- Thống kê chi tiết theo thời gian

### Tích hợp
- Zalo OA API cho thông báo đơn hàng
- Facebook Messenger API
- Google OAuth đăng nhập
- Redis cho cache và queue
- Intervention Image cho xử lý ảnh

## Cài đặt

### Yêu cầu hệ thống
- PHP ≥ 8.2
- MySQL 8
- Redis
- Node.js 20
- Composer

### Bước 1: Clone và cài đặt dependencies

```bash
git clone <repository-url>
cd banhang
composer install
npm install
```

### Bước 2: Cấu hình môi trường

```bash
cp .env.example .env
php artisan key:generate
```

Cập nhật file `.env` với thông tin database và các API keys:

```env
APP_NAME="BanHang"
APP_TIMEZONE=Asia/Ho_Chi_Minh
APP_LOCALE=vi

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=banhang
DB_USERNAME=root
DB_PASSWORD=

QUEUE_CONNECTION=redis
CACHE_STORE=redis

# Google OAuth
GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret

# Zalo Integration
ZALO_OA_ID=your_zalo_oa_id
ZALO_ACCESS_TOKEN=your_zalo_access_token

# Facebook Messenger
FB_PAGE_ID=your_page_id
FB_PAGE_TOKEN=your_page_token
```

### Bước 3: Chạy migrations và seeders

```bash
php artisan migrate
php artisan db:seed
```

### Bước 4: Build assets

```bash
npm run build
```

### Bước 5: Tạo storage link

```bash
php artisan storage:link
```

## Tài khoản mặc định

Sau khi chạy seeders, bạn có thể đăng nhập với:

- **Admin**: admin@example.com / admin12345
- **Staff**: staff@example.com / staff12345  
- **Customer**: customer@example.com / customer12345

## Cấu trúc dự án

```
app/
├── DTOs/                    # Data Transfer Objects
├── Enums/                   # OrderStatus, PaymentMethod
├── Events/                  # OrderPlaced event
├── Http/
│   ├── Controllers/
│   │   ├── Admin/          # Admin controllers
│   │   ├── Api/            # API controllers
│   │   └── Front/          # Frontend controllers
│   ├── Middleware/         # Custom middleware
│   └── Requests/           # Form validation
├── Listeners/              # Event listeners
├── Models/                 # Eloquent models
├── Repositories/           # Data access layer
└── Services/               # Business logic
    ├── Cart/               # Cart management
    ├── Messaging/         # Zalo/Messenger services
    ├── Orders/             # Order processing
    └── Stats/              # Statistics
```

## API Endpoints

### Public API
- `GET /api/search/products` - Tìm kiếm sản phẩm

### Admin API (yêu cầu authentication)
- `GET /api/admin/stats/revenue` - Thống kê doanh thu
- `GET /api/admin/stats/top-products` - Sản phẩm bán chạy

## Queue và Events

Hệ thống sử dụng Redis queue để xử lý:
- Thông báo Zalo/Messenger khi có đơn hàng mới
- Xử lý ảnh sản phẩm
- Gửi email xác nhận

## Cấu hình Zalo/Messenger

1. Truy cập Admin Panel > Settings
2. Nhập thông tin Zalo OA ID và Access Token
3. Nhập thông tin Facebook Page ID và Page Token
4. Test kết nối để đảm bảo hoạt động

## Development

### Chạy development server

```bash
php artisan serve
npm run dev
```

### Chạy queue worker

```bash
php artisan queue:work redis
```

### Chạy tests

```bash
php artisan test
```

## License

MIT License