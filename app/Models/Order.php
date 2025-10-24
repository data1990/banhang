<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'public_id',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'receive_at',
        'note',
        'status',
        'payment_method',
        'payment_status',
        'subtotal',
        'discount',
        'shipping_fee',
        'grand_total',
        'placed_at',
    ];

    protected $casts = [
        'receive_at' => 'datetime',
        'placed_at' => 'datetime',
        'subtotal' => 'integer',
        'discount' => 'integer',
        'shipping_fee' => 'integer',
        'grand_total' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->public_id)) {
                $order->public_id = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(OrderEvent::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDateRange($query, $from, $to)
    {
        return $query->whereBetween('placed_at', [$from, $to]);
    }

    public function getShortIdAttribute(): string
    {
        return substr($this->public_id, 0, 8);
    }

    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao',
            'delivered' => 'Đã giao',
            'canceled' => 'Đã hủy',
            default => $this->status,
        };
    }

    public function getPaymentMethodLabelAttribute(): string
    {
        return match($this->payment_method) {
            'cod' => 'Thanh toán khi nhận hàng',
            'bank_transfer' => 'Chuyển khoản ngân hàng',
            'momo' => 'Ví MoMo',
            default => $this->payment_method,
        };
    }
}
