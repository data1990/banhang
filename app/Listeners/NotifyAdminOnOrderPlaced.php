<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\OrderPlaced;
use App\Services\Messaging\ZaloService;
use App\Services\Messaging\MessengerService;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;

class NotifyAdminOnOrderPlaced implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(
        private ZaloService $zaloService,
        private MessengerService $messengerService
    ) {}

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->fresh('items');
        
        $message = sprintf(
            "ĐƠN MỚI #%s • %s • %s • %s • %s",
            Str::substr($order->public_id, 0, 8),
            number_format($order->grand_total, 0, ',', '.') . 'đ',
            $order->customer_name,
            $order->customer_phone,
            $order->receive_at->format('d/m H:i')
        );

        // Send to Zalo if enabled
        if (Setting::get('zalo.enabled', false)) {
            try {
                $this->zaloService->sendText($message);
            } catch (\Exception $e) {
                \Log::error('Zalo notification failed: ' . $e->getMessage());
            }
        }

        // Send to Messenger if enabled
        if (Setting::get('messenger.enabled', false)) {
            try {
                $this->messengerService->sendText($message);
            } catch (\Exception $e) {
                \Log::error('Messenger notification failed: ' . $e->getMessage());
            }
        }
    }
}
