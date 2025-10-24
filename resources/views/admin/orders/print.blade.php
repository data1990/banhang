<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hóa đơn #{{ $order->short_id }}</title>
    <style>
        body {
            font-family: 'Times New Roman', serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
            background: white;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }
        .header p {
            margin: 5px 0;
            color: #666;
        }
        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }
        .customer-info, .order-info {
            width: 48%;
        }
        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
        }
        .info-section p {
            margin: 3px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-section {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 10px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .total-row.final {
            font-weight: bold;
            font-size: 14px;
            border-top: 1px solid #333;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        @media print {
            body {
                margin: 0;
                padding: 15px;
            }
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>{{ config('app.name', 'BanHang') }}</h1>
        <p>Hệ thống thương mại điện tử</p>
        <p>{{ \App\Models\Setting::get('store.address', '123 Đường ABC, Quận 1, TP.HCM') }}</p>
        <p>ĐT: {{ \App\Models\Setting::get('store.contact_phone', '0123456789') }}</p>
    </div>

    <!-- Invoice Information -->
    <div class="invoice-info">
        <div class="customer-info">
            <div class="info-section">
                <h3>Thông tin khách hàng</h3>
                <p><strong>Tên:</strong> {{ $order->customer_name }}</p>
                <p><strong>SĐT:</strong> {{ $order->customer_phone }}</p>
                <p><strong>Địa chỉ:</strong> {{ $order->customer_address }}</p>
                <p><strong>Nhận hàng:</strong> {{ $order->receive_at->format('d/m/Y H:i') }}</p>
            </div>
        </div>
        
        <div class="order-info">
            <div class="info-section">
                <h3>Thông tin đơn hàng</h3>
                <p><strong>Mã đơn:</strong> #{{ $order->short_id }}</p>
                <p><strong>Ngày đặt:</strong> {{ $order->placed_at->format('d/m/Y H:i') }}</p>
                <p><strong>Thanh toán:</strong> {{ $order->payment_method_label }}</p>
                <p><strong>Trạng thái:</strong> {{ $order->status_label }}</p>
            </div>
        </div>
    </div>

    <!-- Order Items -->
    <table>
        <thead>
            <tr>
                <th style="width: 5%;">STT</th>
                <th style="width: 40%;">Sản phẩm</th>
                <th style="width: 15%;">SKU</th>
                <th style="width: 10%;" class="text-center">Số lượng</th>
                <th style="width: 15%;" class="text-right">Đơn giá</th>
                <th style="width: 15%;" class="text-right">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $index => $item)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $item->product_name }}</td>
                    <td>{{ $item->sku }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-right">{{ money_vnd($item->unit_price) }}</td>
                    <td class="text-right">{{ money_vnd($item->line_total) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Totals -->
    <div class="total-section">
        <div class="total-row">
            <span>Tạm tính:</span>
            <span>{{ money_vnd($order->subtotal) }}</span>
        </div>
        <div class="total-row">
            <span>Phí vận chuyển:</span>
            <span>{{ money_vnd($order->shipping_fee) }}</span>
        </div>
        <div class="total-row">
            <span>Giảm giá:</span>
            <span>-{{ money_vnd($order->discount) }}</span>
        </div>
        <div class="total-row final">
            <span>TỔNG CỘNG:</span>
            <span>{{ money_vnd($order->grand_total) }}</span>
        </div>
    </div>

    <!-- Notes -->
    @if($order->note)
        <div style="margin-top: 20px;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px;">Ghi chú:</h3>
            <p style="margin: 0; padding: 10px; background-color: #f9f9f9; border-left: 3px solid #333;">
                {{ $order->note }}
            </p>
        </div>
    @endif

    <!-- Payment Instructions -->
    @if($order->payment_method === 'bank_transfer')
        <div style="margin-top: 30px; padding: 15px; background-color: #f0f8ff; border: 1px solid #ccc;">
            <h3 style="margin: 0 0 10px 0; font-size: 14px; color: #333;">Hướng dẫn thanh toán:</h3>
            <div style="font-size: 11px;">
                {!! \App\Models\Setting::get('bank.transfer_info', '') !!}
            </div>
            <div style="margin-top: 10px; padding: 8px; background-color: #fff; border: 1px solid #ddd;">
                <strong>Nội dung chuyển khoản:</strong> <code>ORDER-{{ $order->short_id }}</code>
            </div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Cảm ơn bạn đã mua sắm tại {{ config('app.name', 'BanHang') }}!</p>
        <p>In ngày: {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <!-- Print Button -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            <i class="bi bi-printer"></i> In hóa đơn
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">
            Đóng
        </button>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            setTimeout(function() {
                window.print();
            }, 500);
        };
    </script>
</body>
</html>
