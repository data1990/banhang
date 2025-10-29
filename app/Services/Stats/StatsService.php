<?php

declare(strict_types=1);

namespace App\Services\Stats;

use App\Repositories\OrderRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class StatsService
{
    public function __construct(
        private OrderRepository $orderRepository
    ) {}

    public function getRevenueStats(string $from, string $to): array
    {
        return $this->orderRepository->getRevenueStats($from, $to);
    }

    public function getTopProducts(string $from, string $to, int $limit = 10): Collection
    {
        return $this->orderRepository->getTopProducts($from, $to, $limit);
    }

    public function getDailyRevenue(string $from, string $to): array
    {
        $orders = \App\Models\Order::whereBetween('placed_at', [$from, $to])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->selectRaw('DATE(placed_at) as date, SUM(grand_total) as revenue, COUNT(*) as orders_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $orders->map(function ($item) {
            return [
                'date' => $item->date,
                'revenue' => (int) $item->revenue,
                'orders_count' => (int) $item->orders_count,
            ];
        })->toArray();
    }

    public function getMonthlyRevenue(string $from, string $to): array
    {
        $orders = \App\Models\Order::whereBetween('placed_at', [$from, $to])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->selectRaw('YEAR(placed_at) as year, MONTH(placed_at) as month, SUM(grand_total) as revenue, COUNT(*) as orders_count')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return $orders->map(function ($item) {
            return [
                'year' => (int) $item->year,
                'month' => (int) $item->month,
                'revenue' => (int) $item->revenue,
                'orders_count' => (int) $item->orders_count,
            ];
        })->toArray();
    }

    public function getOrderStatusStats(string $from, string $to): array
    {
        $stats = \App\Models\Order::whereBetween('placed_at', [$from, $to])
            ->selectRaw('status, COUNT(*) as count, SUM(grand_total) as total_revenue')
            ->groupBy('status')
            ->get();

        return $stats->map(function ($item) {
            return [
                'status' => $item->status,
                'count' => (int) $item->count,
                'total_revenue' => (int) $item->total_revenue,
            ];
        })->toArray();
    }

    public function getRecentOrders(int $limit = 10): Collection
    {
        return \App\Models\Order::with(['items', 'user'])
            ->orderBy('placed_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getTodayStats(): array
    {
        $today = now()->format('Y-m-d');
        
        $todayOrders = \App\Models\Order::whereDate('placed_at', $today)
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->get();

        $todayPaid = \App\Models\Order::whereDate('placed_at', $today)
            ->where('payment_status', 'paid')
            ->get();

        return [
            'orders_count' => $todayOrders->count(),
            'revenue' => $todayOrders->sum('grand_total'),
            'paid_count' => $todayPaid->count(),
            'paid_amount' => $todayPaid->sum('grand_total'),
        ];
    }

    public function getWeekStats(): array
    {
        $startOfWeek = now()->startOfWeek();
        $endOfWeek = now()->endOfWeek();
        
        $weekOrders = \App\Models\Order::whereBetween('placed_at', [$startOfWeek, $endOfWeek])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->get();

        return [
            'orders_count' => $weekOrders->count(),
            'revenue' => $weekOrders->sum('grand_total'),
        ];
    }

    public function getMonthStats(): array
    {
        $startOfMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();
        
        $monthOrders = \App\Models\Order::whereBetween('placed_at', [$startOfMonth, $endOfMonth])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->get();

        return [
            'orders_count' => $monthOrders->count(),
            'revenue' => $monthOrders->sum('grand_total'),
        ];
    }

    public function getYearStats(): array
    {
        $startOfYear = now()->startOfYear();
        $endOfYear = now()->endOfYear();
        
        $yearOrders = \App\Models\Order::whereBetween('placed_at', [$startOfYear, $endOfYear])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->get();

        return [
            'orders_count' => $yearOrders->count(),
            'revenue' => $yearOrders->sum('grand_total'),
        ];
    }

    public function getScheduledOrdersStats(): array
    {
        $today = now()->format('Y-m-d');
        $tomorrow = now()->addDay()->format('Y-m-d');
        
        // Đơn hàng hẹn giao hôm nay
        $todayScheduled = \App\Models\Order::whereDate('receive_at', $today)
            ->whereIn('status', ['confirmed', 'processing', 'shipped'])
            ->get();

        // Đơn hàng hẹn giao ngày mai
        $tomorrowScheduled = \App\Models\Order::whereDate('receive_at', $tomorrow)
            ->whereIn('status', ['confirmed', 'processing', 'shipped'])
            ->get();

        return [
            'today_scheduled' => [
                'orders_count' => $todayScheduled->count(),
                'total_amount' => $todayScheduled->sum('grand_total'),
                'orders' => $todayScheduled,
            ],
            'tomorrow_scheduled' => [
                'orders_count' => $tomorrowScheduled->count(),
                'total_amount' => $tomorrowScheduled->sum('grand_total'),
                'orders' => $tomorrowScheduled,
            ],
        ];
    }

    public function getTopCustomers(int $limit = 10): array
    {
        $customers = \App\Models\Order::selectRaw('
                customer_name,
                customer_phone,
                customer_email,
                COUNT(*) as total_orders,
                SUM(grand_total) as total_spent,
                MAX(placed_at) as last_order_at
            ')
            ->where('status', 'delivered')
            ->where('payment_status', 'paid')
            ->groupBy('customer_name', 'customer_phone', 'customer_email')
            ->orderBy('total_spent', 'desc')
            ->limit($limit)
            ->get();

        return $customers->map(function ($customer) {
            return (object) [
                'name' => $customer->customer_name,
                'phone' => $customer->customer_phone,
                'email' => $customer->customer_email,
                'total_orders' => (int) $customer->total_orders,
                'total_spent' => (int) $customer->total_spent,
                'last_order_at' => $customer->last_order_at,
            ];
        })->toArray();
    }
}
