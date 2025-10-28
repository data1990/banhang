<?php

declare(strict_types=1);

if (!function_exists('money_vnd')) {
    /**
     * Format money in VND currency
     */
    function money_vnd(int $amount): string
    {
        return number_format($amount, 0, ',', '.') . ' đ';
    }
}

if (!function_exists('format_phone')) {
    /**
     * Format Vietnamese phone number
     */
    function format_phone(?string $phone): string
    {
        if (empty($phone)) {
            return 'N/A';
        }
        
        // Remove all non-digit characters
        $phone = preg_replace('/\D/', '', $phone);
        
        // Add +84 if starts with 0
        if (str_starts_with($phone, '0')) {
            $phone = '+84' . substr($phone, 1);
        }
        
        return $phone;
    }
}

if (!function_exists('generate_order_number')) {
    /**
     * Generate order number from UUID
     */
    function generate_order_number(string $uuid): string
    {
        return 'DH' . strtoupper(substr($uuid, 0, 8));
    }
}

if (!function_exists('getStatusColor')) {
    /**
     * Get Bootstrap color class for order status
     */
    function getStatusColor(string $status): string
    {
        return match($status) {
            'pending' => 'warning',
            'confirmed' => 'info',
            'processing' => 'primary',
            'shipped' => 'success',
            'delivered' => 'success',
            'canceled' => 'danger',
            default => 'secondary',
        };
    }
}

if (!function_exists('getStatusLabel')) {
    /**
     * Get Vietnamese label for order status
     */
    function getStatusLabel(string $status): string
    {
        return match($status) {
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'processing' => 'Đang xử lý',
            'shipped' => 'Đang giao',
            'delivered' => 'Đã giao',
            'canceled' => 'Đã hủy',
            default => $status,
        };
    }
}

if (!function_exists('product_image_url')) {
    /**
     * Get product image URL with specific size
     */
    function product_image_url(string $path, string $size = 'medium'): string
    {
        $imageService = app(\App\Services\Media\ImageService::class);
        return $imageService->getImageUrlBySize($path, $size);
    }
}

if (!function_exists('product_thumbnail_url')) {
    /**
     * Get product thumbnail URL
     */
    function product_thumbnail_url(string $path): string
    {
        $imageService = app(\App\Services\Media\ImageService::class);
        return $imageService->getThumbnailUrl($path);
    }
}

if (!function_exists('setting')) {
    /**
     * Get setting value by key
     */
    function setting(string $key, mixed $default = null): mixed
    {
        return \App\Models\Setting::get($key, $default);
    }
}
