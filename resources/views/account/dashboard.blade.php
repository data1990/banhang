@extends('layouts.app')

@section('title', 'Tài khoản của tôi')

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
                    <h4 class="mb-0">Chào mừng, {{ auth()->user()->name }}!</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-receipt" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2">{{ auth()->user()->orders->count() }}</h5>
                                    <p class="mb-0">Tổng đơn hàng</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-success text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-check-circle" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2">{{ auth()->user()->orders->where('status', 'delivered')->count() }}</h5>
                                    <p class="mb-0">Đã giao</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body text-center">
                                    <i class="bi bi-clock" style="font-size: 2rem;"></i>
                                    <h5 class="mt-2">{{ auth()->user()->orders->whereIn('status', ['pending', 'confirmed', 'processing', 'shipped'])->count() }}</h5>
                                    <p class="mb-0">Đang xử lý</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Orders -->
                    <h5 class="mt-4">Đơn hàng gần đây</h5>
                    @if(auth()->user()->orders->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Mã đơn hàng</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach(auth()->user()->orders->take(5) as $order)
                                        <tr>
                                            <td>#{{ $order->short_id }}</td>
                                            <td>{{ $order->placed_at->format('d/m/Y') }}</td>
                                            <td>{{ money_vnd($order->grand_total) }}</td>
                                            <td>
                                                <span class="badge bg-{{ getStatusColor($order->status) }}">
                                                    {{ $order->status_label }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('account.order-detail', $order->public_id) }}" 
                                                   class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-eye"></i> Xem
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('account.orders') }}" class="btn btn-primary">
                                Xem tất cả đơn hàng
                            </a>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-3">Bạn chưa có đơn hàng nào</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary">
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
