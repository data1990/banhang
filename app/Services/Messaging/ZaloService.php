<?php

declare(strict_types=1);

namespace App\Services\Messaging;

use App\Models\Setting;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ZaloService
{
    private string $baseUrl = 'https://openapi.zalo.me/v2.0/oa/message';

    public function sendText(string $message): bool
    {
        $oaId = Setting::get('zalo.oa_id');
        $accessToken = Setting::get('zalo.access_token');

        if (!$oaId || !$accessToken) {
            throw new \Exception('Zalo configuration missing');
        }

        try {
            $response = Http::withHeaders([
                'access_token' => $accessToken,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl, [
                'recipient' => [
                    'user_id' => $oaId
                ],
                'message' => [
                    'text' => $message
                ]
            ]);

            if ($response->successful()) {
                Log::info('Zalo message sent successfully');
                return true;
            }

            Log::error('Zalo API error: ' . $response->body());
            return false;

        } catch (\Exception $e) {
            Log::error('Zalo service error: ' . $e->getMessage());
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
