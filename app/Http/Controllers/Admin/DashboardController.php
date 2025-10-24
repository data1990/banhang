<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Stats\StatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private StatsService $statsService
    ) {}

    public function index(Request $request): View
    {
        $from = $request->get('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));

        // Revenue stats
        $revenueStats = $this->statsService->getRevenueStats($from, $to);
        
        // Top products
        $topProducts = $this->statsService->getTopProducts($from, $to, 10);
        
        // Order status stats
        $orderStatusStats = $this->statsService->getOrderStatusStats($from, $to);
        
        // Recent orders
        $recentOrders = $this->statsService->getRecentOrders(10);
        
        // Daily revenue for chart
        $dailyRevenue = $this->statsService->getDailyRevenue($from, $to);

        return view('admin.dashboard', compact(
            'revenueStats',
            'topProducts',
            'orderStatusStats',
            'recentOrders',
            'dailyRevenue',
            'from',
            'to'
        ));
    }
}
