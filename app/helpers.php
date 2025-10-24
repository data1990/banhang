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
    function format_phone(string $phone): string
    {
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
