<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderEvent;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class OrderRepository
{
    public function create(array $data): Order
    {
        return Order::create($data);
    }

    public function find(int $id): ?Order
    {
        return Order::with(['items', 'user', 'events'])->find($id);
    }

    public function findByPublicId(string $publicId): ?Order
    {
        return Order::with(['items', 'user', 'events'])->where('public_id', $publicId)->first();
    }

    public function findByUser(int $userId, int $perPage = 15): LengthAwarePaginator
    {
        return Order::where('user_id', $userId)
            ->with(['items'])
            ->orderBy('placed_at', 'desc')
            ->paginate($perPage);
    }

    public function findByStatus(string $status, int $perPage = 15): LengthAwarePaginator
    {
        return Order::where('status', $status)
            ->with(['items', 'user'])
            ->orderBy('placed_at', 'desc')
            ->paginate($perPage);
    }

    public function findByDateRange(string $from, string $to, int $perPage = 15): LengthAwarePaginator
    {
        return Order::whereBetween('placed_at', [$from, $to])
            ->with(['items', 'user'])
            ->orderBy('placed_at', 'desc')
            ->paginate($perPage);
    }

    public function getRevenueStats(string $from, string $to): array
    {
        $orders = Order::whereBetween('placed_at', [$from, $to])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->get();

        return [
            'total_revenue' => $orders->sum('grand_total'),
            'total_orders' => $orders->count(),
            'average_order_value' => $orders->count() > 0 ? $orders->sum('grand_total') / $orders->count() : 0,
        ];
    }

    public function getTopProducts(string $from, string $to, int $limit = 10): Collection
    {
        return Order::whereBetween('placed_at', [$from, $to])
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->join('order_items', 'orders.id', '=', 'order_items.order_id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->selectRaw('
                products.id,
                products.name,
                products.sku,
                SUM(order_items.qty) as total_qty,
                SUM(order_items.line_total) as total_revenue
            ')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderBy('total_qty', 'desc')
            ->limit($limit)
            ->get();
    }

    public function createItem(int $orderId, array $data): OrderItem
    {
        $product = Product::find($data['product_id']);
        
        return OrderItem::create([
            'order_id' => $orderId,
            'product_id' => $data['product_id'],
            'product_name' => $product->name ?? 'Unknown Product',
            'sku' => $product->sku ?? 'N/A',
            'qty' => $data['quantity'],
            'unit_price' => $data['price'],
            'line_total' => $data['subtotal'],
        ]);
    }

    public function createEvent(int $orderId, array $data): OrderEvent
    {
        // Handle both old format (status/note) and new format (event/data)
        if (isset($data['event']) && isset($data['data'])) {
            // New format with event and data
            return OrderEvent::create([
                'order_id' => $orderId,
                'actor_id' => $data['created_by'] ?? null,
                'event' => $data['event'],
                'data' => $data['data'],
            ]);
        } else {
            // Old format with status and note (backward compatibility)
            return OrderEvent::create([
                'order_id' => $orderId,
                'actor_id' => $data['created_by'] ?? null,
                'event' => 'status_changed',
                'data' => [
                    'old_status' => null,
                    'new_status' => $data['status'] ?? null,
                    'note' => $data['note'] ?? null,
                ],
            ]);
        }
    }
}
