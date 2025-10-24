<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Repositories\CartRepository;
use Illuminate\Support\Facades\DB;

class CartService
{
    public function __construct(
        private CartRepository $cartRepository
    ) {}

    public function getOrCreateCart(?int $userId, string $sessionToken): Cart
    {
        $cart = $this->cartRepository->findByUserOrSession($userId, $sessionToken);
        
        if (!$cart) {
            $cart = $this->cartRepository->create([
                'user_id' => $userId,
                'session_token' => $sessionToken,
                'expires_at' => now()->addDays(7),
            ]);
        }

        return $cart;
    }

    public function addItem(Cart $cart, Product $product, int $qty): CartItem
    {
        // Check stock availability
        if ($product->stock < $qty) {
            throw new \Exception('Không đủ hàng trong kho');
        }

        // Check if item already exists
        $existingItem = $cart->items()->where('product_id', $product->id)->first();

        if ($existingItem) {
            $newQty = $existingItem->qty + $qty;
            if ($product->stock < $newQty) {
                throw new \Exception('Không đủ hàng trong kho');
            }
            
            $existingItem->update([
                'qty' => $newQty,
                'unit_price_snapshot' => $product->current_price,
            ]);
            
            return $existingItem;
        }

        return $cart->items()->create([
            'product_id' => $product->id,
            'qty' => $qty,
            'unit_price_snapshot' => $product->current_price,
        ]);
    }

    public function updateItemQuantity(Cart $cart, int $productId, int $qty): CartItem
    {
        $item = $cart->items()->where('product_id', $productId)->firstOrFail();
        
        if ($qty <= 0) {
            $item->delete();
            return $item;
        }

        $product = Product::findOrFail($productId);
        if ($product->stock < $qty) {
            throw new \Exception('Không đủ hàng trong kho');
        }

        $item->update([
            'qty' => $qty,
            'unit_price_snapshot' => $product->current_price,
        ]);

        return $item;
    }

    public function removeItem(Cart $cart, int $productId): bool
    {
        return $cart->items()->where('product_id', $productId)->delete() > 0;
    }

    public function clearCart(Cart $cart): void
    {
        $cart->items()->delete();
    }

    public function mergeCarts(Cart $guestCart, Cart $userCart): Cart
    {
        return DB::transaction(function () use ($guestCart, $userCart) {
            foreach ($guestCart->items as $item) {
                $existingItem = $userCart->items()->where('product_id', $item->product_id)->first();
                
                if ($existingItem) {
                    $existingItem->update([
                        'qty' => $existingItem->qty + $item->qty,
                        'unit_price_snapshot' => $item->product->current_price,
                    ]);
                } else {
                    $userCart->items()->create([
                        'product_id' => $item->product_id,
                        'qty' => $item->qty,
                        'unit_price_snapshot' => $item->product->current_price,
                    ]);
                }
            }

            $guestCart->delete();
            return $userCart;
        });
    }

    public function recalculatePrices(Cart $cart): void
    {
        foreach ($cart->items as $item) {
            $product = $item->product;
            $item->update([
                'unit_price_snapshot' => $product->current_price,
            ]);
        }
    }
}
