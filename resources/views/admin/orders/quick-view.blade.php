<div class="row">
    <!-- Order Info -->
    <div class="col-md-6 mb-3">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-info-circle"></i> Thông tin đơn hàng</h6>
                <div class="mb-2">
                    <small class="text-muted">Mã đơn:</small>
                    <div class="fw-bold">{{ $order->public_id }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Ngày đặt:</small>
                    <div>{{ $order->placed_at->format('d/m/Y H:i') }}</div>
                </div>
                @if($order->receive_at)
                <div class="mb-2">
                    <small class="text-muted">Hẹn giao:</small>
                    <div class="text-danger fw-bold">{{ \Carbon\Carbon::parse($order->receive_at)->format('d/m/Y') }}</div>
                </div>
                @endif
                <div class="mb-2">
                    <small class="text-muted">Trạng thái:</small>
                    <div>
                        <span class="badge bg-{{ getStatusColor($order->status) }}">
                            {{ getStatusLabel($order->status) }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Info -->
    <div class="col-md-6 mb-3">
        <div class="card bg-light">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="bi bi-person"></i> Thông tin khách hàng</h6>
                <div class="mb-2">
                    <small class="text-muted">Tên khách hàng:</small>
                    <div class="fw-bold">{{ $order->customer_name }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">SĐT:</small>
                    <div><a href="tel:{{ $order->customer_phone }}">{{ format_phone($order->customer_phone) }}</a></div>
                </div>
                @if($order->customer_email)
                <div class="mb-2">
                    <small class="text-muted">Email:</small>
                    <div><a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a></div>
                </div>
                @endif
                <div class="mb-2">
                    <small class="text-muted">Địa chỉ giao hàng:</small>
                    <div class="small">{{ $order->customer_address }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="mb-3">
    <h6 class="fw-bold mb-3"><i class="bi bi-cart"></i> Chi tiết sản phẩm</h6>
    <div class="table-responsive">
        <table class="table table-sm table-bordered">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Sản phẩm</th>
                    <th class="text-end">Đơn giá</th>
                    <th class="text-center">SL</th>
                    <th class="text-end">Thành tiền</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>
                        <div>{{ $item->product_name }}</div>
                        <small class="text-muted">SKU: {{ $item->sku }}</small>
                    </td>
                    <td class="text-end">{{ money_vnd($item->unit_price) }}</td>
                    <td class="text-center">{{ $item->qty }}</td>
                    <td class="text-end">{{ money_vnd($item->line_total) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="4" class="text-end">Tạm tính:</th>
                    <th class="text-end">{{ money_vnd($order->subtotal) }}</th>
                </tr>
                @if($order->discount > 0)
                <tr>
                    <td colspan="4" class="text-end">Giảm giá:</td>
                    <td class="text-end text-danger">-{{ money_vnd($order->discount) }}</td>
                </tr>
                @endif
                @if($order->shipping_fee > 0)
                <tr>
                    <td colspan="4" class="text-end">Phí vận chuyển:</td>
                    <td class="text-end">{{ money_vnd($order->shipping_fee) }}</td>
                </tr>
                @endif
                <tr class="table-primary">
                    <th colspan="4" class="text-end">Tổng cộng:</th>
                    <th class="text-end">{{ money_vnd($order->grand_total) }}</th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Payment Info -->
<div class="row mb-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-2">Phương thức thanh toán</h6>
                <p class="mb-0">{{ $order->payment_method_label }}</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-2">Trạng thái thanh toán</h6>
                <p class="mb-0">
                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : ($order->payment_status == 'refunded' ? 'danger' : 'warning') }}">
                        {{ $order->payment_status_label }}
                    </span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Notes -->
@if($order->note)
<div class="alert alert-info">
    <strong><i class="bi bi-sticky"></i> Ghi chú:</strong>
    <p class="mb-0 mt-2">{{ $order->note }}</p>
</div>
@endif

<!-- Actions -->
<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-eye"></i> Xem chi tiết đầy đủ
    </a>
    <a href="{{ route('admin.orders.print', $order) }}" class="btn btn-secondary btn-sm" target="_blank">
        <i class="bi bi-printer"></i> In đơn hàng
    </a>
</div>

