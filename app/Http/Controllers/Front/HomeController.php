<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Services\Stats\StatsService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __construct(
        private StatsService $statsService
    ) {}

    public function index(): View
    {
        // New products (last 30 days)
        $newProducts = Product::active()
            ->inStock()
            ->where('created_at', '>=', now()->subDays(30))
            ->with(['images' => function ($query) {
                $query->primary()->ordered();
            }])
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();

        // Best selling products (last 30 days)
        $bestSellingProducts = $this->statsService->getTopProducts(
            now()->subDays(30)->format('Y-m-d'),
            now()->format('Y-m-d'),
            8
        );

        // Categories
        $categories = Category::active()
            ->ordered()
            ->with(['products' => function ($query) {
                $query->active()->inStock()->limit(4);
            }])
            ->get();

        return view('front.home', compact('newProducts', 'bestSellingProducts', 'categories'));
    }
}
