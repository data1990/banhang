<?php

declare(strict_types=1);

namespace App\DTOs;

class CheckoutDTO
{
    public function __construct(
        public readonly ?int $userId,
        public readonly string $sessionToken,
        public readonly string $customerName,
        public readonly string $customerPhone,
        public readonly string $customerAddress,
        public readonly string $receiveAt,
        public readonly ?string $note,
        public readonly string $paymentMethod,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            userId: $data['user_id'] ?? null,
            sessionToken: $data['session_token'] ?? '',
            customerName: $data['customer_name'],
            customerPhone: $data['customer_phone'],
            customerAddress: $data['customer_address'],
            receiveAt: $data['receive_at'],
            note: $data['note'] ?? null,
            paymentMethod: $data['payment_method'],
        );
    }
}
