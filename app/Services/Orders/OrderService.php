<?php

declare(strict_types=1);

namespace App\Services\Orders;

use App\DTOs\CheckoutDTO;
use App\Events\OrderPlaced;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function __construct(
        private CartRepository $cartRepository,
        private OrderRepository $orderRepository
    ) {}

    public function placeOrder(?int $userId, CheckoutDTO $dto): Order
    {
        return DB::transaction(function () use ($userId, $dto) {
            // Lock cart for processing
            $cart = $this->cartRepository->findByUserOrSession($userId, $dto->sessionToken);
            
            if (!$cart || $cart->items->isEmpty()) {
                throw new \Exception('Giỏ hàng trống');
            }

            // Recalculate prices
            $this->recalculateCartPrices($cart);

            // Check stock availability
            $this->assertStockAvailable($cart);

            // Create order
            $order = $this->createOrder($userId, $dto, $cart);

            // Create order items
            $this->createOrderItems($order, $cart);

            // Decrement stock
            $this->decrementStock($cart);

            // Fire event
            event(new OrderPlaced($order));

            // Clear cart
            $this->cartRepository->delete($cart->id);

            return $order->load('items');
        });
    }

    private function recalculateCartPrices(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $product = $item->product;
            $item->update([
                'unit_price_snapshot' => $product->current_price,
            ]);
        }
    }

    private function assertStockAvailable(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            if ($item->product->stock < $item->qty) {
                throw new \Exception("Sản phẩm {$item->product->name} không đủ hàng trong kho");
            }
        }
    }

    private function createOrder(?int $userId, CheckoutDTO $dto, Cart $cart): Order
    {
        $subtotal = $cart->total;
        $shippingFee = 0; // Free shipping for now
        $discount = 0; // No discount for now
        $grandTotal = $subtotal + $shippingFee - $discount;

        return $this->orderRepository->create([
            'user_id' => $userId,
            'customer_name' => $dto->customerName,
            'customer_phone' => $dto->customerPhone,
            'customer_address' => $dto->customerAddress,
            'receive_at' => $dto->receiveAt,
            'note' => $dto->note,
            'payment_method' => $dto->paymentMethod,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping_fee' => $shippingFee,
            'grand_total' => $grandTotal,
            'placed_at' => now(),
        ]);
    }

    private function createOrderItems(Order $order, Cart $cart): void
    {
        foreach ($cart->items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'product_name' => $item->product->name,
                'sku' => $item->product->sku,
                'unit_price' => $item->unit_price_snapshot,
                'qty' => $item->qty,
                'line_total' => $item->line_total,
            ]);
        }
    }

    private function decrementStock(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $item->product->decrement('stock', $item->qty);
        }
    }

    public function updateOrderStatus(Order $order, string $status, ?int $actorId = null, ?string $note = null): Order
    {
        $oldStatus = $order->status;
        
        $order->update(['status' => $status]);

        // Log the status change
        $order->events()->create([
            'actor_id' => $actorId,
            'event' => 'status_changed',
            'data' => [
                'old_status' => $oldStatus,
                'new_status' => $status,
                'note' => $note,
            ],
        ]);

        return $order;
    }

    /**
     * Create order from admin panel
     */
    public function createOrderFromAdmin(array $data): Order
    {
        return DB::transaction(function () use ($data) {
            // Validate required data
            if (empty($data['items']) || !is_array($data['items'])) {
                throw new \InvalidArgumentException('Items array is required');
            }

            // Check stock availability before creating order
            foreach ($data['items'] as $item) {
                $product = Product::find($item['product_id']);
                if (!$product) {
                    throw new \Exception("Không tìm thấy sản phẩm ID: {$item['product_id']}");
                }
                if ($product->stock < ($item['quantity'] ?? 0)) {
                    throw new \Exception("Sản phẩm {$product->name} không đủ hàng trong kho. Số lượng tồn: {$product->stock}");
                }
            }

            // Calculate totals
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
            }
            
            $shippingFee = 0; // Free shipping for now
            $discount = 0; // No discount for now
            $grandTotal = $subtotal + $shippingFee - $discount;

            // Parse receive_at safely
            $receiveAt = now()->addDays(1); // Default
            if (!empty($data['receive_at'])) {
                try {
                    $receiveAt = \Carbon\Carbon::parse($data['receive_at']);
                } catch (\Exception $e) {
                    // Keep default if parsing fails
                }
            }

            // Create order
            $order = $this->orderRepository->create([
                'user_id' => $data['customer_id'] ?? null,
                'customer_name' => $data['customer_name'] ?? '',
                'customer_phone' => $data['customer_phone'] ?? '',
                'customer_address' => $data['shipping_address'] ?? '',
                'customer_email' => $data['customer_email'] ?? null,
                'receive_at' => $receiveAt,
                'note' => $data['notes'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'cod',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'shipping_fee' => $shippingFee,
                'grand_total' => $grandTotal,
                'placed_at' => now(),
                'status' => 'confirmed', // Admin orders are auto-confirmed
            ]);

            // Create order items and decrement stock
            foreach ($data['items'] as $item) {
                $this->orderRepository->createItem($order->id, [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['price'] * $item['quantity'],
                ]);
                
                // Decrement product stock
                $product = Product::find($item['product_id']);
                if ($product) {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            // Create order event
            $this->orderRepository->createEvent($order->id, [
                'status' => 'confirmed',
                'note' => 'Đơn hàng được tạo bởi admin',
                'created_by' => auth()->id(),
            ]);

            return $order;
        });
    }

    /**
     * Create order event
     */
    public function createOrderEvent(int $orderId, array $data): void
    {
        $this->orderRepository->createEvent($orderId, $data);
    }
}
