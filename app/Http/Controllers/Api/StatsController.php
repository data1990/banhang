<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Stats\StatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatsController extends Controller
{
    public function __construct(
        private StatsService $statsService
    ) {}

    public function revenue(Request $request): JsonResponse
    {
        $from = $request->get('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));
        $groupBy = $request->get('groupBy', 'day');

        $data = match($groupBy) {
            'day' => $this->statsService->getDailyRevenue($from, $to),
            'month' => $this->statsService->getMonthlyRevenue($from, $to),
            default => $this->statsService->getDailyRevenue($from, $to),
        };

        return response()->json([
            'success' => true,
            'data' => $data,
        ]);
    }

    public function topProducts(Request $request): JsonResponse
    {
        $from = $request->get('from', now()->subDays(30)->format('Y-m-d'));
        $to = $request->get('to', now()->format('Y-m-d'));
        $limit = $request->get('limit', 10);

        $products = $this->statsService->getTopProducts($from, $to, $limit);

        return response()->json([
            'success' => true,
            'data' => $products,
        ]);
    }
}
