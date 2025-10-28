<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Loyalty\LoyaltyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class CustomersController extends Controller
{
    public function __construct(
        private LoyaltyService $loyaltyService
    ) {}

    public function index(Request $request): View
    {
        // Get unique customers from orders based on phone or email
        $query = DB::table('orders')
            ->select(
                'customer_name',
                'customer_phone',
                'customer_email',
                DB::raw('COUNT(*) as total_orders'),
                DB::raw('SUM(grand_total) as total_spent'),
                DB::raw('MAX(placed_at) as last_order_at')
            )
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->groupBy('customer_name', 'customer_phone', 'customer_email');

        // Filter by phone or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%");
            });
        }

        // Get the data
        $customerData = $query->orderBy('total_orders', 'desc')->get();

        // Calculate stars for each customer
        $customers = $customerData->map(function ($data) {
            $stars = $this->loyaltyService->calculateStars((int) $data->total_orders);
            $isVip = $stars >= (int) setting('loyalty_vip_stars', 5);
            
            return (object) [
                'name' => $data->customer_name,
                'phone' => $data->customer_phone,
                'email' => $data->customer_email,
                'total_orders' => (int) $data->total_orders,
                'total_spent' => (int) $data->total_spent,
                'stars' => $stars,
                'is_vip' => $isVip,
                'last_order_at' => $data->last_order_at,
            ];
        });

        // Manual pagination
        $perPage = 20;
        $currentPage = $request->get('page', 1);
        $items = $customers->forPage($currentPage, $perPage);
        $total = $customers->count();
        
        $customers = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin.customers.index', compact('customers'));
    }

    public function show(Request $request): View
    {
        $phone = $request->get('phone');
        $email = $request->get('email');
        
        // Get all orders for this customer
        $orders = Order::where(function ($q) use ($phone, $email) {
            if ($phone) {
                $q->where('customer_phone', $phone);
            }
            if ($email) {
                $q->orWhere('customer_email', $email);
            }
        })
        ->with(['items.product'])
        ->orderBy('placed_at', 'desc')
        ->get();

        if ($orders->isEmpty()) {
            abort(404, 'Không tìm thấy khách hàng');
        }

        $firstOrder = $orders->first();
        $customer = (object) [
            'name' => $firstOrder->customer_name,
            'phone' => $firstOrder->customer_phone,
            'email' => $firstOrder->customer_email,
            'total_orders' => $orders->count(),
            'total_spent' => $orders->sum('grand_total'),
            'stars' => $this->loyaltyService->calculateStars($orders->count()),
            'is_vip' => $this->loyaltyService->calculateStars($orders->count()) >= (int) setting('loyalty_vip_stars', 5),
            'last_order_at' => $orders->first()->placed_at,
        ];

        return view('admin.customers.show', compact('customer', 'orders'));
    }
}
