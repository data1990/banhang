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
}
