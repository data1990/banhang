@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Quick Stats - Today -->
<div class="row mb-4">
    <div class="col-md-12 mb-3">
        <h5 class="text-muted">
            <i class="bi bi-calendar-check"></i> Hôm nay - {{ now()->format('d/m/Y') }}
        </h5>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-primary shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Đơn hàng hôm nay
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($todayStats['orders_count']) }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-wallet2"></i> Đã thanh toán: {{ $todayStats['paid_count'] }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt-cutoff text-primary" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-success shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Doanh thu hôm nay
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            {{ money_vnd($todayStats['revenue']) }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-cash-coin"></i> Đã thu: {{ money_vnd($todayStats['paid_amount']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-exchange text-success" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-warning shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Doanh thu tuần này
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            {{ money_vnd($weekStats['revenue']) }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-cart-check"></i> {{ $weekStats['orders_count'] }} đơn hàng
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up-arrow text-warning" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-info shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Doanh thu tháng này
                        </div>
                        <div class="h4 mb-0 font-weight-bold text-gray-800">
                            {{ money_vnd($monthStats['revenue']) }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-cart-check"></i> {{ $monthStats['orders_count'] }} đơn hàng
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up text-info" style="font-size: 2.5rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scheduled Orders - Today & Tomorrow -->
<div class="row mb-4">
    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-danger shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-bold text-danger">
                        <i class="bi bi-calendar-x"></i> Hẹn giao hôm nay
                    </h6>
                    <span class="badge bg-danger">{{ $scheduledStats['today_scheduled']['orders_count'] }}</span>
                </div>
                <div class="h4 mb-2 fw-bold">{{ number_format($scheduledStats['today_scheduled']['orders_count']) }} đơn hàng</div>
                <div class="text-muted mb-3">
                    <i class="bi bi-currency-exchange"></i> Tổng giá trị: {{ money_vnd($scheduledStats['today_scheduled']['total_amount']) }}
                </div>
                @if($scheduledStats['today_scheduled']['orders_count'] > 0)
                    <div class="list-group list-group-flush">
                        @foreach($scheduledStats['today_scheduled']['orders']->take(3) as $order)
                            <div class="list-group-item px-0 py-2 border-0 order-item" 
                                 style="cursor: pointer;" 
                                 onclick="showOrderDetail({{ $order->id }})">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold">#{{ $order->short_id }}</span>
                                        <span class="text-muted small ms-2">{{ $order->customer_name }}</span>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-danger">{{ money_vnd($order->grand_total) }}</div>
                                        <span class="badge bg-{{ getStatusColor($order->status) }} small">
                                            {{ getStatusLabel($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($scheduledStats['today_scheduled']['orders_count'] > 3)
                        <div class="text-center mt-2">
                            <small class="text-muted">+{{ $scheduledStats['today_scheduled']['orders_count'] - 3 }} đơn hàng khác</small>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0">Không có đơn hàng nào cần giao hôm nay</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-xl-6 col-md-6 mb-4">
        <div class="card border-info shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <h6 class="mb-0 fw-bold text-info">
                        <i class="bi bi-calendar-check"></i> Hẹn giao ngày mai
                    </h6>
                    <span class="badge bg-info">{{ $scheduledStats['tomorrow_scheduled']['orders_count'] }}</span>
                </div>
                <div class="h4 mb-2 fw-bold">{{ number_format($scheduledStats['tomorrow_scheduled']['orders_count']) }} đơn hàng</div>
                <div class="text-muted mb-3">
                    <i class="bi bi-currency-exchange"></i> Tổng giá trị: {{ money_vnd($scheduledStats['tomorrow_scheduled']['total_amount']) }}
                </div>
                @if($scheduledStats['tomorrow_scheduled']['orders_count'] > 0)
                    <div class="list-group list-group-flush">
                        @foreach($scheduledStats['tomorrow_scheduled']['orders']->take(3) as $order)
                            <div class="list-group-item px-0 py-2 border-0 order-item" 
                                 style="cursor: pointer;" 
                                 onclick="showOrderDetail({{ $order->id }})">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="fw-bold">#{{ $order->short_id }}</span>
                                        <span class="text-muted small ms-2">{{ $order->customer_name }}</span>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-bold text-info">{{ money_vnd($order->grand_total) }}</div>
                                        <span class="badge bg-{{ getStatusColor($order->status) }} small">
                                            {{ getStatusLabel($order->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($scheduledStats['tomorrow_scheduled']['orders_count'] > 3)
                        <div class="text-center mt-2">
                            <small class="text-muted">+{{ $scheduledStats['tomorrow_scheduled']['orders_count'] - 3 }} đơn hàng khác</small>
                        </div>
                    @endif
                @else
                    <div class="text-center py-3">
                        <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        <p class="text-muted mt-2 mb-0">Không có đơn hàng nào cần giao ngày mai</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-secondary shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            Doanh thu năm {{ now()->year }}
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ money_vnd($yearStats['revenue']) }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-bar-chart"></i> {{ $yearStats['orders_count'] }} đơn hàng
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-calendar-year text-secondary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-danger shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Giá trị đơn hàng TB (30 ngày)
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ money_vnd($revenueStats['average_order_value']) }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-list-ol"></i> Tổng: {{ $revenueStats['total_orders'] }} đơn
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-percent text-danger" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-warning shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Đơn đang xử lý
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\Order::whereIn('status', ['pending', 'confirmed', 'processing'])->count() }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-hourglass-split"></i> Cần xử lý ngay
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock-history text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-success shadow-sm">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Đơn đang giao
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\Order::where('status', 'shipped')->count() }}
                        </div>
                        <div class="text-muted small">
                            <i class="bi bi-truck"></i> Đang trên đường
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-truck-flatbed text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Biểu đồ doanh thu</h6>
                <div class="dropdown no-arrow">
                    <a class="dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-three-dots-vertical"></i>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right shadow">
                        <a class="dropdown-item" href="#" onclick="updateChart('day')">Theo ngày</a>
                        <a class="dropdown-item" href="#" onclick="updateChart('week')">Theo tuần</a>
                        <a class="dropdown-item" href="#" onclick="updateChart('month')">Theo tháng</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Pie Chart -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                <h6 class="m-0 font-weight-bold text-primary">Trạng thái đơn hàng</h6>
            </div>
            <div class="card-body">
                <div class="chart-pie pt-4 pb-2">
                    <canvas id="orderStatusChart"></canvas>
                </div>
                <div class="mt-4 text-center small">
                    @foreach($orderStatusStats as $stat)
                        <span class="mr-2">
                            <i class="bi bi-circle-fill" style="color: {{ getStatusColor($stat['status']) }}"></i>
                            {{ getStatusLabel($stat['status']) }}: {{ $stat['count'] }}
                        </span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Products -->
    <div class="col-xl-12 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sản phẩm bán chạy</h6>
            </div>
            <div class="card-body">
                @if($topProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 50px;">STT</th>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">SKU</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-end">Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $index => $product)
                                    <tr>
                                        <td>
                                            <span class="badge bg-primary">{{ $index + 1 }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                @php
                                                    $productModel = \App\Models\Product::with('images')->find($product->id);
                                                    $imageUrl = $productModel && $productModel->images->count() > 0 
                                                        ? product_thumbnail_url($productModel->images->first()->path)
                                                        : asset('storage/products/placeholder_thumb.jpg');
                                                @endphp
                                                <img src="{{ $imageUrl }}" 
                                                     alt="{{ $product->name }}" 
                                                     class="rounded me-3" 
                                                     style="width: 50px; height: 50px; object-fit: cover;">
                                                <div>
                                                    <div class="fw-bold small">{{ $product->name }}</div>
                                                    <small class="text-muted">ID: {{ $product->id }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <code class="text-muted">{{ $product->sku }}</code>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-info fs-6">{{ number_format($product->total_qty) }}</span>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            {{ money_vnd($product->total_revenue) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-box-seam text-muted" style="font-size: 4rem;"></i>
                        <p class="text-muted mt-3 mb-2">Chưa có dữ liệu sản phẩm bán chạy</p>
                        <small class="text-muted">Sản phẩm bán chạy sẽ hiển thị khi có đơn hàng</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Top Customers -->
    <div class="col-xl-6 col-lg-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-success">
                    <i class="bi bi-trophy"></i> Top khách hàng
                </h6>
                @if(count($topCustomers) > 0)
                    <span class="badge bg-success">{{ count($topCustomers) }}</span>
                @endif
            </div>
            <div class="card-body">
                @if(count($topCustomers) > 0)
                    @foreach(array_slice($topCustomers, 0, 10) as $index => $customer)
                        <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="flex-shrink-0">
                                <div class="rank-badge bg-{{ $index < 3 ? 'warning' : 'secondary' }} text-white rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 14px;">
                                    {{ $index + 1 }}
                                </div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold">{{ $customer->name }}</div>
                                @if($customer->phone)
                                    <div class="text-muted small">
                                        <i class="bi bi-telephone"></i> {{ format_phone($customer->phone) }}
                                    </div>
                                @endif
                                <div class="text-success small fw-bold">
                                    <i class="bi bi-cash-coin"></i> {{ money_vnd($customer->total_spent) }}
                                </div>
                            </div>
                            <div class="flex-shrink-0 text-end">
                                <div class="fw-bold text-success">{{ money_vnd($customer->total_spent) }}</div>
                                <div class="text-muted small">{{ number_format($customer->total_orders) }} đơn</div>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.customers.index') }}" class="btn btn-sm btn-success">
                            <i class="bi bi-people"></i> Xem tất cả khách hàng
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">Chưa có dữ liệu khách hàng</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Order Detail Modal -->
<div class="modal fade" id="orderDetailModal" tabindex="-1" aria-labelledby="orderDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="orderDetailModalLabel">Chi tiết đơn hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="orderDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart');
let revenueChart;

// Check if there's data to display
const dailyRevenueLabels = {!! json_encode(array_column($dailyRevenue, 'date')) !!};
const dailyRevenueData = {!! json_encode(array_column($dailyRevenue, 'revenue')) !!};

if (revenueCtx && dailyRevenueLabels.length > 0) {
    revenueChart = new Chart(revenueCtx, {
        type: 'line',
        data: {
            labels: dailyRevenueLabels,
            datasets: [{
                label: 'Doanh thu (VNĐ)',
                data: dailyRevenueData,
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('vi-VN').format(value);
                        }
                    }
                }
            }
        }
    });
} else if (revenueCtx) {
    revenueCtx.style.height = '400px';
    revenueCtx.parentElement.innerHTML = `
        <div class="text-center py-5">
            <i class="bi bi-graph-up text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3">Chưa có dữ liệu doanh thu để hiển thị</p>
            <small class="text-muted">Biểu đồ sẽ hiển thị khi có đơn hàng</small>
        </div>
    `;
}

// Order Status Chart
const statusCtx = document.getElementById('orderStatusChart');
let statusChart;

const orderStatusLabels = {!! json_encode(array_map('getStatusLabel', array_column($orderStatusStats, 'status'))) !!};
const orderStatusData = {!! json_encode(array_column($orderStatusStats, 'count')) !!};

if (statusCtx && orderStatusData.length > 0 && orderStatusData.some(d => d > 0)) {
    statusChart = new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: orderStatusLabels,
            datasets: [{
                data: orderStatusData,
                backgroundColor: [
                    '#dc3545', // pending - red
                    '#0d6efd', // confirmed - blue
                    '#ffc107', // processing - yellow
                    '#198754', // shipped - green
                    '#20c997', // delivered - teal
                    '#6c757d'  // canceled - gray
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 15,
                        usePointStyle: true
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed || 0;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} đơn (${percentage}%)`;
                        }
                    }
                }
            }
        }
    });
} else if (statusCtx) {
    statusCtx.style.height = '400px';
    statusCtx.parentElement.parentElement.innerHTML = `
        <div class="text-center py-5">
            <i class="bi bi-pie-chart text-muted" style="font-size: 4rem;"></i>
            <p class="text-muted mt-3">Chưa có dữ liệu trạng thái đơn hàng</p>
            <small class="text-muted">Biểu đồ sẽ hiển thị khi có đơn hàng</small>
        </div>
    `;
}

function updateChart(period) {
    // This would typically make an AJAX request to get new data
    console.log('Updating chart for period:', period);
}

// Order Detail Modal Function
function showOrderDetail(orderId) {
    const modal = new bootstrap.Modal(document.getElementById('orderDetailModal'));
    const content = document.getElementById('orderDetailContent');
    
    // Show loading spinner
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    // Fetch order details via AJAX
    fetch(`/admin/orders/${orderId}/quick-view`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                content.innerHTML = data.html;
            } else {
                content.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle"></i> ${data.message}
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle"></i> Đã xảy ra lỗi khi tải thông tin đơn hàng
                </div>
            `;
        });
}

// Add hover effect to order items
document.querySelectorAll('.order-item').forEach(item => {
    item.addEventListener('mouseenter', function() {
        this.style.backgroundColor = '#f8f9fa';
    });
    item.addEventListener('mouseleave', function() {
        this.style.backgroundColor = '';
    });
});
</script>
@endsection

