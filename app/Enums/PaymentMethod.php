<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case COD = 'cod';
    case BANK_TRANSFER = 'bank_transfer';
    case MOMO = 'momo';

    public function label(): string
    {
        return match($this) {
            self::COD => 'Thanh toán khi nhận hàng',
            self::BANK_TRANSFER => 'Chuyển khoản ngân hàng',
            self::MOMO => 'Ví MoMo',
        };
    }

    public function icon(): string
    {
        return match($this) {
            self::COD => 'cash',
            self::BANK_TRANSFER => 'credit-card',
            self::MOMO => 'smartphone',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
