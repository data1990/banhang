@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

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
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Đơn hàng của tôi</h4>
                </div>
                <div class="card-body">
                    @if(auth()->user()->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đơn hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Sản phẩm</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(auth()->user()->orders as $order)
                                        <tr>
                                            <td>
                                                <strong>#{{ $order->short_id }}</strong>
                                                <br>
                                                <small class="text-muted">{{ $order->payment_method_label }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $order->placed_at->format('d/m/Y') }}</div>
                                                <small class="text-muted">{{ $order->placed_at->format('H:i') }}</small>
                                            </td>
                                            <td>
                                                <div>{{ $order->items->count() }} sản phẩm</div>
                                                <small class="text-muted">
                                                    {{ $order->items->first()->product_name ?? '' }}
                                                    @if($order->items->count() > 1)
                                                        và {{ $order->items->count() - 1 }} sản phẩm khác
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">{{ money_vnd($order->grand_total) }}</span>
                                            </td>
                                            <td>
                                                <span class="badge bg-{{ getStatusColor($order->status) }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('account.order-detail', $order->public_id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Chi tiết
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted" style="font-size: 4rem;"></i>
                            <h4 class="text-muted mt-3">Chưa có đơn hàng nào</h4>
                            <p class="text-muted">Hãy bắt đầu mua sắm để tạo đơn hàng đầu tiên</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                                <i class="bi bi-shop"></i> Mua sắm ngay
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

