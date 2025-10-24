<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'public_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'price',
        'sale_price',
        'stock',
        'is_active',
        'short_desc',
        'long_desc',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
        'sale_price' => 'integer',
        'stock' => 'integer',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($product) {
            if (empty($product->public_id)) {
                $product->public_id = \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }

    public function primaryImage(): HasMany
    {
        return $this->hasMany(ProductImage::class)->where('is_primary', true);
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }

    public function getCurrentPriceAttribute(): int
    {
        return $this->sale_price ?? $this->price;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->sale_price !== null && $this->sale_price < $this->price;
    }
}
