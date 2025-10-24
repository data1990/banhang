<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Collection;

class CartRepository
{
    public function findByUserOrSession(?int $userId, string $sessionToken): ?Cart
    {
        $query = Cart::query()->notExpired();

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->where('session_token', $sessionToken);
        }

        return $query->with('items.product')->first();
    }

    public function create(array $data): Cart
    {
        return Cart::create($data);
    }

    public function find(int $id): ?Cart
    {
        return Cart::with('items.product')->find($id);
    }

    public function findByUser(int $userId): Collection
    {
        return Cart::where('user_id', $userId)
            ->notExpired()
            ->with('items.product')
            ->get();
    }

    public function findBySession(string $sessionToken): Collection
    {
        return Cart::where('session_token', $sessionToken)
            ->notExpired()
            ->with('items.product')
            ->get();
    }

    public function delete(int $id): bool
    {
        return Cart::destroy($id) > 0;
    }

    public function cleanupExpired(): int
    {
        return Cart::where('expires_at', '<', now())->delete();
    }
}
