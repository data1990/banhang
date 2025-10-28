<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Orders\OrderService;
use App\Http\Requests\Admin\CreateOrderRequest;
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

    public function updatePaymentStatus(Request $request, Order $order): RedirectResponse
    {
        $request->validate([
            'payment_status' => 'required|in:unpaid,paid,refunded',
            'note' => 'nullable|string|max:500',
        ]);

        try {
            $oldStatus = $order->payment_status;
            $newStatus = $request->payment_status;
            
            $order->update(['payment_status' => $newStatus]);

            // Create order event
            $this->orderService->createOrderEvent($order->id, [
                'event' => 'payment_status_changed',
                'data' => [
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'note' => $request->note,
                ],
                'created_by' => auth()->id(),
            ]);

            $statusLabel = match($newStatus) {
                'paid' => 'đã thanh toán',
                'unpaid' => 'chưa thanh toán',
                'refunded' => 'đã hoàn tiền',
                default => $newStatus,
            };

            return back()->with('success', "Trạng thái thanh toán đã được cập nhật thành: {$statusLabel}");
        } catch (\Exception $e) {
            return back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function print(Order $order): View
    {
        $order->load(['items.product', 'user']);
        
        return view('admin.orders.print', compact('order'));
    }

    public function create(): View
    {
        $products = Product::active()->with('images')->get();
        $customers = User::where('role', 'customer')->get();
        
        return view('admin.orders.create', compact('products', 'customers'));
    }

    public function store(CreateOrderRequest $request): RedirectResponse
    {
        try {
            $order = $this->orderService->createOrderFromAdmin($request->validated());
            
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Đơn hàng đã được tạo thành công');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
