<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Orders\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrdersController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    public function index(Request $request): View
    {
        $query = Order::with(['items', 'user']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->filled('from')) {
            $query->where('placed_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $query->where('placed_at', '<=', $request->to);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('public_id', 'like', "%{$search}%");
            });
        }

        $orders = $query->orderBy('placed_at', 'desc')->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order): View
    {
        $order->load(['items.product', 'user', 'events.actor']);
        
        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,canceled',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $this->orderService->updateOrderStatus(
                $order,
                $request->status,
                auth()->id(),
                $request->note
            );

            return back()->with('success', 'Trạng thái đơn hàng đã được cập nhật');

        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function print(Order $order): View
    {
        $order->load(['items.product', 'user']);
        
        return view('admin.orders.print', compact('order'));
    }
}
