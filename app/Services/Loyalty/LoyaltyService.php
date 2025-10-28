<?php

declare(strict_types=1);

namespace App\Services\Loyalty;

use App\Models\CustomerLoyaltyProfile;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    public function calculateStars(int $totalOrders): float
    {
        $ordersPerStar = (int) setting('loyalty_orders_per_star', 10);
        $ordersPerHalfStar = (int) setting('loyalty_orders_per_half_star', 5);
        $maxStars = (int) setting('loyalty_max_stars', 10);

        $stars = 0;

        if ($totalOrders == 0) {
            return 0;
        }

        // Always give at least 0.5 stars for the first order
        if ($totalOrders == 1) {
            return 0.5;
        }

        // Calculate stars based on orders
        if ($totalOrders >= $ordersPerStar) {
            // Full stars
            $stars = floor($totalOrders / $ordersPerStar);
            
            // Check for half star
            $remainder = $totalOrders % $ordersPerStar;
            if ($remainder >= $ordersPerHalfStar) {
                $stars += 0.5;
            }
        } elseif ($totalOrders >= $ordersPerHalfStar) {
            // Half star only (for 5-9 orders)
            $stars = 0.5;
        } else {
            // 2-4 orders still get 0.5 stars to show appreciation
            $stars = 0.5;
        }

        // Cap at max stars
        if ($stars > $maxStars) {
            $stars = $maxStars;
        }

        return $stars;
    }

    public function updateLoyaltyProfile(User $user): CustomerLoyaltyProfile
    {
        $orders = Order::where('user_id', $user->id)
            ->whereIn('status', ['confirmed', 'processing', 'shipped', 'delivered'])
            ->get();

        $totalOrders = $orders->count();
        $totalSpent = $orders->sum('grand_total');
        $stars = $this->calculateStars($totalOrders);
        $isVip = $stars >= (int) setting('loyalty_vip_stars', 5);
        
        $lastOrder = $orders->orderBy('placed_at', 'desc')->first();
        $lastOrderAt = $lastOrder ? $lastOrder->placed_at : null;

        return CustomerLoyaltyProfile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'total_orders' => $totalOrders,
                'total_spent' => $totalSpent,
                'stars' => $stars,
                'is_vip' => $isVip,
                'last_order_at' => $lastOrderAt,
            ]
        );
    }

    public function recalculateAllCustomers(): int
    {
        $customers = User::where('role', 'customer')->get();
        $updated = 0;

        foreach ($customers as $customer) {
            $this->updateLoyaltyProfile($customer);
            $updated++;
        }

        return $updated;
    }
}

