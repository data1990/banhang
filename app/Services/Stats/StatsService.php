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
}
