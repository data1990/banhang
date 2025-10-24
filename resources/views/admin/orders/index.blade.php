@extends('layouts.admin')

@section('title', 'Quản lý đơn hàng')
@section('page-title', 'Quản lý đơn hàng')

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.orders.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Tên, SĐT, mã đơn hàng...">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="status" class="form-label">Trạng thái</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Tất cả</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Chờ xác nhận</option>
                        <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                        <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                        <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đang giao</option>
                        <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Đã giao</option>
                        <option value="canceled" {{ request('status') == 'canceled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label for="from" class="form-label">Từ ngày</label>
                    <input type="date" class="form-control" id="from" name="from" 
                           value="{{ request('from') }}">
                </div>
                <div class="col-md-2 mb-3">
                    <label for="to" class="form-label">Đến ngày</label>
                    <input type="date" class="form-control" id="to" name="to" 
                           value="{{ request('to') }}">
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="submit" class="btn btn-outline-primary">Lọc</button>
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">Xóa bộ lọc</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Orders Table -->
<div class="card">
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Khách hàng</th>
                            <th>Số điện thoại</th>
                            <th>Thời gian đặt</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td>
                                    <div>
                                        <strong>#{{ $order->short_id }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $order->payment_method_label }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">{{ $order->customer_name }}</h6>
                                        <small class="text-muted">{{ Str::limit($order->customer_address, 30) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-muted">{{ $order->customer_phone }}</span>
                                </td>
                                <td>
                                    <div>
                                        <div>{{ $order->placed_at->format('d/m/Y') }}</div>
                                        <small class="text-muted">{{ $order->placed_at->format('H:i') }}</small>
                                    </div>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">{{ money_vnd($order->grand_total) }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-status bg-{{ getStatusColor($order->status) }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.orders.show', $order) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.orders.print', $order) }}" 
                                           class="btn btn-sm btn-outline-info" 
                                           data-bs-toggle="tooltip" title="In hóa đơn" target="_blank">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $orders->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">Chưa có đơn hàng nào</h4>
                <p class="text-muted">Các đơn hàng sẽ hiển thị ở đây</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
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
