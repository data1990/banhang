<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Messaging\ZaloService;
use App\Services\Messaging\MessengerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(
        private ZaloService $zaloService,
        private MessengerService $messengerService
    ) {}

    public function index(): View
    {
        $settings = [
            'bank' => [
                'transfer_info' => Setting::get('bank.transfer_info', ''),
            ],
            'zalo' => [
                'oa_id' => Setting::get('zalo.oa_id', ''),
                'access_token' => Setting::get('zalo.access_token', ''),
                'enabled' => Setting::get('zalo.enabled', false),
            ],
            'messenger' => [
                'page_id' => Setting::get('messenger.page_id', ''),
                'page_token' => Setting::get('messenger.page_token', ''),
                'enabled' => Setting::get('messenger.enabled', false),
            ],
            'store' => [
                'contact_phone' => Setting::get('store.contact_phone', ''),
                'address' => Setting::get('store.address', ''),
                'messenger_link' => Setting::get('store.messenger_link', ''),
                'zalo_link' => Setting::get('store.zalo_link', ''),
            ],
        ];

        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'bank_transfer_info' => 'nullable|string',
            'zalo_oa_id' => 'nullable|string|max:255',
            'zalo_access_token' => 'nullable|string|max:255',
            'zalo_enabled' => 'boolean',
            'messenger_page_id' => 'nullable|string|max:255',
            'messenger_page_token' => 'nullable|string|max:255',
            'messenger_enabled' => 'boolean',
            'store_contact_phone' => 'nullable|string|max:50',
            'store_address' => 'nullable|string|max:255',
            'store_messenger_link' => 'nullable|url',
            'store_zalo_link' => 'nullable|url',
        ]);

        // Update settings
        Setting::set('bank.transfer_info', $request->bank_transfer_info);
        
        Setting::set('zalo.oa_id', $request->zalo_oa_id);
        Setting::set('zalo.access_token', $request->zalo_access_token);
        Setting::set('zalo.enabled', $request->boolean('zalo_enabled'));
        
        Setting::set('messenger.page_id', $request->messenger_page_id);
        Setting::set('messenger.page_token', $request->messenger_page_token);
        Setting::set('messenger.enabled', $request->boolean('messenger_enabled'));
        
        Setting::set('store.contact_phone', $request->store_contact_phone);
        Setting::set('store.address', $request->store_address);
        Setting::set('store.messenger_link', $request->store_messenger_link);
        Setting::set('store.zalo_link', $request->store_zalo_link);

        return back()->with('success', 'Cài đặt đã được cập nhật thành công');
    }

    public function testZalo(): RedirectResponse
    {
        try {
            $success = $this->zaloService->testConnection();
            
            if ($success) {
                return back()->with('success', 'Kết nối Zalo thành công');
            } else {
                return back()->with('error', 'Kết nối Zalo thất bại');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi kết nối Zalo: ' . $e->getMessage());
        }
    }

    public function testMessenger(): RedirectResponse
    {
        try {
            $success = $this->messengerService->testConnection();
            
            if ($success) {
                return back()->with('success', 'Kết nối Messenger thành công');
            } else {
                return back()->with('error', 'Kết nối Messenger thất bại');
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Lỗi kết nối Messenger: ' . $e->getMessage());
        }
    }
}
