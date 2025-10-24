@extends('layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats border-left-primary">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Tổng doanh thu
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ money_vnd($revenueStats['total_revenue']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar text-primary" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats success">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Tổng đơn hàng
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ number_format($revenueStats['total_orders']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-receipt text-success" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats warning">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Giá trị đơn hàng TB
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ money_vnd($revenueStats['average_order_value']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-graph-up text-warning" style="font-size: 2rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card card-stats danger">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Đơn chờ xử lý
                        </div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ \App\Models\Order::where('status', 'pending')->count() }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-clock text-danger" style="font-size: 2rem;"></i>
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
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Sản phẩm bán chạy</h6>
            </div>
            <div class="card-body">
                @if($topProducts->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>SKU</th>
                                    <th>Số lượng bán</th>
                                    <th>Doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="bi bi-box text-muted"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $product->name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $product->sku }}</td>
                                        <td>
                                            <span class="badge bg-primary">{{ number_format($product->total_qty) }}</span>
                                        </td>
                                        <td class="fw-bold text-success">{{ money_vnd($product->total_revenue) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-graph-down text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">Chưa có dữ liệu bán chạy</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Đơn hàng gần đây</h6>
            </div>
            <div class="card-body">
                @if($recentOrders->count() > 0)
                    @foreach($recentOrders as $order)
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <i class="bi bi-receipt text-primary"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <div class="fw-bold">#{{ $order->short_id }}</div>
                                <div class="text-muted small">{{ $order->customer_name }}</div>
                                <div class="text-muted small">{{ money_vnd($order->grand_total) }}</div>
                            </div>
                            <div class="flex-shrink-0">
                                <span class="badge badge-status bg-{{ getStatusColor($order->status) }}">
                                    {{ $order->status_label }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-primary">
                            Xem tất cả đơn hàng
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="bi bi-inbox text-muted" style="font-size: 3rem;"></i>
                        <p class="text-muted mt-3">Chưa có đơn hàng nào</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($dailyRevenue, 'date')) !!},
        datasets: [{
            label: 'Doanh thu',
            data: {!! json_encode(array_column($dailyRevenue, 'revenue')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                    }
                }
            }
        }
    }
});

// Order Status Chart
const statusCtx = document.getElementById('orderStatusChart').getContext('2d');
const statusChart = new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode(array_map('getStatusLabel', array_column($orderStatusStats, 'status'))) !!},
        datasets: [{
            data: {!! json_encode(array_column($orderStatusStats, 'count')) !!},
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#4BC0C0',
                '#9966FF',
                '#FF9F40'
            ]
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

function updateChart(period) {
    // This would typically make an AJAX request to get new data
    console.log('Updating chart for period:', period);
}
</script>
@endsection

