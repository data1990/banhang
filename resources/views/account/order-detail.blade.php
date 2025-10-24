@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->short_id)

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tài khoản</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('account.dashboard') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Tổng quan
                        </a>
                        <a href="{{ route('account.profile') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('account.profile') ? 'active' : '' }}">
                            <i class="bi bi-person"></i> Thông tin cá nhân
                        </a>
                        <a href="{{ route('account.orders') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('account.orders*') ? 'active' : '' }}">
                            <i class="bi bi-receipt"></i> Đơn hàng của tôi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Order Header -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Đơn hàng #{{ $order->short_id }}</h4>
                    <span class="badge bg-{{ getStatusColor($order->status) }} fs-6">
                        {{ $order->status_label }}
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Thông tin đơn hàng:</h6>
                            <p class="text-muted mb-1">
                                <strong>Ngày đặt:</strong> {{ $order->placed_at->format('d/m/Y H:i') }}
                            </p>
                            <p class="text-muted mb-1">
                                <strong>Thanh toán:</strong> {{ $order->payment_method_label }}
                            </p>
                            <p class="text-muted mb-1">
                                <strong>Nhận hàng:</strong> {{ $order->receive_at->format('d/m/Y H:i') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Thông tin giao hàng:</h6>
                            <p class="text-muted mb-1">
                                <strong>Người nhận:</strong> {{ $order->customer_name }}
                            </p>
                            <p class="text-muted mb-1">
                                <strong>SĐT:</strong> {{ $order->customer_phone }}
                            </p>
                            <p class="text-muted mb-0">
                                <strong>Địa chỉ:</strong> {{ $order->customer_address }}
                            </p>
                        </div>
                    </div>
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

            <!-- Order Notes -->
            @if($order->note)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Ghi chú đơn hàng</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted mb-0">{{ $order->note }}</p>
                    </div>
                </div>
            @endif

            <!-- Order Timeline -->
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
                                        @if($event->data && isset($event->data['note']))
                                            <small class="text-muted">
                                                <strong>Ghi chú:</strong> {{ $event->data['note'] }}
                                            </small>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('account.orders') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại danh sách
                        </a>
                        <div>
                            @if($order->status === 'pending')
                                <span class="text-muted">
                                    <i class="bi bi-info-circle"></i> Đơn hàng đang chờ xác nhận
                                </span>
                            @elseif($order->status === 'delivered')
                                <span class="text-success">
                                    <i class="bi bi-check-circle"></i> Đơn hàng đã được giao thành công
                                </span>
                            @else
                                <span class="text-primary">
                                    <i class="bi bi-clock"></i> Đơn hàng đang được xử lý
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

