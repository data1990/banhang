@extends('layouts.admin')

@section('title', 'Quản lý khách hàng')
@section('page-title', 'Danh sách khách hàng')

@section('content')
<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.customers.index') }}" class="row g-3">
            <div class="col-md-10">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên, email, SĐT..." 
                       value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Tìm kiếm
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Customers Table -->
<div class="card shadow">
    <div class="card-body">
        @if($customers->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Khách hàng</th>
                            <th class="text-center">Sao</th>
                            <th class="text-center">Số đơn</th>
                            <th class="text-end">Tổng chi tiêu</th>
                            <th class="text-center">Đơn cuối</th>
                            <th class="text-end">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customers as $customer)
                            @php
                                // Ensure at least 0 for calculation
                                $stars = max(0, $customer->stars);
                                $fullStars = floor($stars);
                                $hasHalfStar = ($stars - $fullStars) >= 0.5;
                            @endphp
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary rounded-circle me-3 d-flex align-items-center justify-content-center text-white fw-bold">
                                            {{ strtoupper(substr($customer->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-bold">{{ $customer->name }}</div>
                                            <small class="text-muted">
                                                @if($customer->phone)
                                                    <i class="bi bi-telephone"></i> {{ format_phone($customer->phone) }}<br>
                                                @endif
                                                @if($customer->email)
                                                    <i class="bi bi-envelope"></i> {{ $customer->email }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="stars">
                                        @php
                                            $colors = ['#6c757d', '#17a2b8', '#28a745', '#ffc107', '#fd7e14', '#dc3545', '#6f42c1', '#e83e8c', '#20c997', '#0dcaf0'];
                                        @endphp
                                        
                                        @for($i = 0; $i < $fullStars; $i++)
                                            @php
                                                $color = ($i < count($colors)) ? $colors[$i] : '#ffc107';
                                            @endphp
                                            <i class="bi bi-star-fill" style="color: {{ $color }};"></i>
                                        @endfor
                                        
                                        @if($hasHalfStar)
                                            @php
                                                $halfStarColor = ($fullStars < count($colors)) ? $colors[$fullStars] : '#ffc107';
                                            @endphp
                                            <i class="bi bi-star-half" style="color: {{ $halfStarColor }};"></i>
                                        @endif
                                        
                                        @if($customer->is_vip)
                                            <span class="badge bg-danger ms-1">VIP</span>
                                        @endif
                                    </div>
                                    <small class="text-muted">{{ number_format($customer->total_orders) }} đơn</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill">{{ number_format($customer->total_orders) }} đơn</span>
                                </td>
                                <td class="text-end">
                                    <span class="fw-bold text-success">{{ money_vnd($customer->total_spent) }}</span>
                                </td>
                                <td class="text-center">
                                    @if($customer->last_order_at)
                                        <small>{{ \Carbon\Carbon::parse($customer->last_order_at)->format('d/m/Y') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('admin.customers.show', ['phone' => $customer->phone, 'email' => $customer->email]) }}" class="btn btn-sm btn-primary">
                                        <i class="bi bi-eye"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $customers->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people text-muted" style="font-size: 4rem;"></i>
                <p class="text-muted mt-3">Không tìm thấy khách hàng nào</p>
            </div>
        @endif
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 14px;
}
.stars {
    font-size: 1.5rem;
    line-height: 1;
}
.stars i {
    transition: transform 0.2s ease;
    cursor: pointer;
}
.stars i:hover {
    transform: scale(1.2);
}
</style>
@endsection

