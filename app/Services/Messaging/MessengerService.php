<?php

declare(strict_types=1);

namespace App\Services\Messaging;

use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MessengerService
{
    private string $baseUrl = 'https://graph.facebook.com/v18.0';

    public function sendText(string $message): bool
    {
        $pageId = Setting::get('messenger.page_id');
        $pageToken = Setting::get('messenger.page_token');

        if (!$pageId || !$pageToken) {
            throw new \Exception('Messenger configuration missing');
        }

        try {
            $response = Http::post("{$this->baseUrl}/{$pageId}/messages", [
                'recipient' => [
                    'id' => $pageId
                ],
                'message' => [
                    'text' => $message
                ],
                'access_token' => $pageToken
            ]);

            if ($response->successful()) {
                Log::info('Messenger message sent successfully');
                return true;
            }

            Log::error('Messenger API error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Messenger service error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function testConnection(): bool
    {
        try {
            $this->sendText('Test connection from BanHang system');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}
