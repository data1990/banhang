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
use Illuminate\Support\Facades\DB;
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

    public function quickView(Order $order): \Illuminate\Http\JsonResponse
    {
        $order->load(['items.product', 'user']);
        
        $html = view('admin.orders.quick-view', compact('order'))->render();
        
        return response()->json([
            'success' => true,
            'html' => $html
        ]);
    }

    public function create(): View
    {
        $products = Product::active()->with('images')->get();
        
        // Get unique customers from orders with their latest address
        $customers = DB::select("
            SELECT 
                customer_name,
                customer_phone,
                customer_email,
                customer_address,
                MAX(placed_at) as last_order_date
            FROM orders 
            WHERE status IN ('confirmed', 'processing', 'shipped', 'delivered')
            GROUP BY customer_name, customer_phone, customer_email, customer_address
            ORDER BY customer_name
        ");
        
        // Get the most recent address for each unique customer
        $customers = collect($customers)->map(function ($customer) {
            // Get the latest order for this customer to get the most recent address
            $latestOrder = Order::where('customer_name', $customer->customer_name)
                ->where('customer_phone', $customer->customer_phone)
                ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
                ->orderBy('placed_at', 'desc')
                ->first();
            
            return (object) [
                'name' => $customer->customer_name,
                'phone' => $customer->customer_phone,
                'email' => $customer->customer_email,
                'address' => $latestOrder ? $latestOrder->customer_address : ($customer->customer_address ?? ''),
            ];
        });
        
        return view('admin.orders.create', compact('products', 'customers'));
    }

    public function store(CreateOrderRequest $request): RedirectResponse
    {
        try {
            $data = $request->validated();
            
            // Set customer_id to null if it's empty string (not a user ID)
            if (empty($data['customer_id']) || $data['customer_id'] === '') {
                $data['customer_id'] = null;
            }
            
            $order = $this->orderService->createOrderFromAdmin($data);
            
            return redirect()->route('admin.orders.show', $order)
                ->with('success', 'Đơn hàng đã được tạo thành công');
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }

    public function destroy(Order $order): RedirectResponse
    {
        try {
            $order->delete();
            return redirect()->route('admin.orders.index')->with('success', 'Đã xóa đơn hàng');
        } catch (\Exception $e) {
            return back()->with('error', 'Không thể xóa đơn hàng: ' . $e->getMessage());
        }
    }
}
