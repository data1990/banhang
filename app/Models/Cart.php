<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_token',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForSession($query, $sessionToken)
    {
        return $query->where('session_token', $sessionToken);
    }

    public function scopeNotExpired($query)
    {
        return $query->where('expires_at', '>', now());
    }

    public function getTotalAttribute(): int
    {
        return $this->items->sum(function ($item) {
            return $item->qty * $item->unit_price_snapshot;
        });
    }

    public function getItemCountAttribute(): int
    {
        return $this->items->sum('qty');
    }
}
