<?php

declare(strict_types=1);

namespace App\Enums;

enum OrderStatus: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELED = 'canceled';

    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Chờ xác nhận',
            self::CONFIRMED => 'Đã xác nhận',
            self::PROCESSING => 'Đang xử lý',
            self::SHIPPED => 'Đang giao',
            self::DELIVERED => 'Đã giao',
            self::CANCELED => 'Đã hủy',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::PENDING => 'warning',
            self::CONFIRMED => 'info',
            self::PROCESSING => 'primary',
            self::SHIPPED => 'success',
            self::DELIVERED => 'success',
            self::CANCELED => 'danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
