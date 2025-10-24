@extends('layouts.admin')

@section('title', 'Chi tiết đơn hàng #' . $order->short_id)
@section('page-title', 'Chi tiết đơn hàng #' . $order->short_id)

@section('content')
<div class="row">
    <!-- Order Information -->
    <div class="col-lg-8">
        <!-- Customer Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thông tin khách hàng</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Họ tên:</h6>
                        <p class="text-muted">{{ $order->customer_name }}</p>
                        
                        <h6>Số điện thoại:</h6>
                        <p class="text-muted">{{ $order->customer_phone }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6>Địa chỉ nhận hàng:</h6>
                        <p class="text-muted">{{ $order->customer_address }}</p>
                        
                        <h6>Thời gian nhận hàng:</h6>
                        <p class="text-muted">{{ $order->receive_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
                
                @if($order->note)
                    <hr>
                    <h6>Ghi chú:</h6>
                    <p class="text-muted">{{ $order->note }}</p>
                @endif
            </div>
        </div>

        <!-- Order Items -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Sản phẩm trong đơn hàng</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>SKU</th>
                                <th>Số lượng</th>
                                <th>Đơn giá</th>
                                <th>Thành tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                            </div>
                                            <div>
                                                <h6 class="mb-1">{{ $item->product_name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td><code>{{ $item->sku }}</code></td>
                                    <td>{{ $item->qty }}</td>
                                    <td>{{ money_vnd($item->unit_price) }}</td>
                                    <td class="fw-bold">{{ money_vnd($item->line_total) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Tạm tính:</td>
                                <td class="fw-bold">{{ money_vnd($order->subtotal) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Phí vận chuyển:</td>
                                <td>{{ money_vnd($order->shipping_fee) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end">Giảm giá:</td>
                                <td class="text-danger">-{{ money_vnd($order->discount) }}</td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                                <td class="fw-bold text-primary h5">{{ money_vnd($order->grand_total) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Order Events -->
        @if($order->events->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Lịch sử đơn hàng</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    @foreach($order->events->sortBy('created_at') as $event)
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-circle-fill text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-1">{{ $event->event }}</h6>
                                <p class="text-muted mb-1">{{ $event->created_at->format('d/m/Y H:i') }}</p>
                                @if($event->actor)
                                    <small class="text-muted">Bởi: {{ $event->actor->name }}</small>
                                @endif
                                @if($event->data)
                                    <div class="mt-2">
                                        <small class="text-muted">
                                            @if(isset($event->data['note']))
                                                <strong>Ghi chú:</strong> {{ $event->data['note'] }}
                                            @endif
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Order Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Trạng thái đơn hàng</h5>
            </div>
            <div class="card-body">
                <div class="text-center mb-3">
                    <span class="badge badge-status bg-{{ getStatusColor($order->status) }} fs-6">
                        {{ $order->status_label }}
                    </span>
                </div>

                <!-- Status Update Form -->
                <form method="POST" action="{{ route('admin.orders.update-status', $order) }}">
                    @csrf
                    <div class="mb-3">
                        <label for="status" class="form-label">Cập nhật trạng thái</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                            <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                            <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                            <option value="shipped" {{ $order->status == 'shipped' ? 'selected' : '' }}>Đang giao</option>
                            <option value="delivered" {{ $order->status == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                            <option value="canceled" {{ $order->status == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">Ghi chú (không bắt buộc)</label>
                        <textarea class="form-control" id="note" name="note" rows="3" 
                                  placeholder="Ghi chú về việc thay đổi trạng thái..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-check-circle"></i> Cập nhật trạng thái
                    </button>
                </form>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Tóm tắt đơn hàng</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Mã đơn hàng:</span>
                    <strong>#{{ $order->short_id }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Ngày đặt:</span>
                    <span>{{ $order->placed_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Phương thức thanh toán:</span>
                    <span>{{ $order->payment_method_label }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Trạng thái thanh toán:</span>
                    <span class="badge bg-{{ $order->payment_status == 'paid' ? 'success' : 'warning' }}">
                        {{ $order->payment_status == 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' }}
                    </span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fw-bold">
                    <span>Tổng tiền:</span>
                    <span class="text-primary">{{ money_vnd($order->grand_total) }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Thao tác</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.orders.print', $order) }}" 
                       class="btn btn-outline-primary" target="_blank">
                        <i class="bi bi-printer"></i> In hóa đơn
                    </a>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Status update form
    const statusForm = document.querySelector('form[action*="update-status"]');
    if (statusForm) {
        statusForm.addEventListener('submit', function(e) {
            const status = document.getElementById('status').value;
            const currentStatus = '{{ $order->status }}';
            
            if (status === currentStatus) {
                e.preventDefault();
                alert('Trạng thái hiện tại đã được chọn');
                return false;
            }
            
            if (!confirm('Bạn có chắc muốn cập nhật trạng thái đơn hàng?')) {
                e.preventDefault();
                return false;
            }
        });
    }
});
</script>
@endsection

@php
function getStatusColor($status) {
    return match($status) {
        'pending' => 'warning',
        'confirmed' => 'info',
        'processing' => 'primary',
        'shipped' => 'success',
        'delivered' => 'success',
        'canceled' => 'danger',
        default => 'secondary',
    };
}
@endphp
