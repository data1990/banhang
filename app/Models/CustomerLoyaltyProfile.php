<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerLoyaltyProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total_orders',
        'total_spent',
        'stars',
        'is_vip',
        'last_order_at',
    ];

    protected $casts = [
        'is_vip' => 'boolean',
        'last_order_at' => 'datetime',
        'total_spent' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getStarsDisplayAttribute(): string
    {
        $fullStars = floor($this->stars);
        $hasHalfStar = ($this->stars - $fullStars) >= 0.5;
        
        $display = str_repeat('⭐', $fullStars);
        
        if ($hasHalfStar) {
            $display .= '✨';
        }
        
        return $display;
    }

    public function getTotalSpentFormattedAttribute(): string
    {
        return money_vnd($this->total_spent);
    }
}
