<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'bank.transfer_info' => '<h4>Thông tin chuyển khoản</h4><p>Ngân hàng: Vietcombank<br>STK: 1234567890<br>Tên: CÔNG TY TNHH BÁN HÀNG<br>Nội dung: ORDER-{ORDER_ID}</p>',
            'zalo.oa_id' => '',
            'zalo.access_token' => '',
            'zalo.enabled' => false,
            'messenger.page_id' => '',
            'messenger.page_token' => '',
            'messenger.enabled' => false,
            'store.contact_phone' => '0123456789',
            'store.address' => '123 Đường ABC, Quận 1, TP.HCM',
            'store.messenger_link' => 'https://m.me/yourpage',
            'store.zalo_link' => 'https://zalo.me/yourzalo',
        ];

        foreach ($settings as $key => $value) {
            Setting::set($key, $value);
        }
    }
}
