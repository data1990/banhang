<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\DTOs\CheckoutDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Setting;
use App\Services\Cart\CartService;
use App\Services\Orders\OrderService;
use App\Services\VietQR\VietQRService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService,
        private VietQRService $vietQRService
    ) {}

    public function show(Request $request): View|RedirectResponse
    {
        $cart = $this->cartService->getOrCreateCart(
            auth()->id(),
            $request->session()->getId()
        );

        if ($cart->items->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống');
        }

        $cart->load(['items.product.images' => function ($query) {
            $query->primary()->ordered();
        }]);

        $user = auth()->user();
        $profile = $user?->profile;

        // Bank transfer info
        $bankInfo = Setting::get('bank.transfer_info', '');

        return view('front.checkout.show', compact('cart', 'user', 'profile', 'bankInfo'));
    }

    public function placeOrder(CheckoutRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            
            $dto = new CheckoutDTO(
                userId: auth()->id(),
                sessionToken: $request->session()->getId(),
                customerName: $validated['customer_name'],
                customerPhone: $validated['customer_phone'],
                customerAddress: $validated['customer_address'],
                receiveAt: $validated['receive_at'],
                note: $validated['note'] ?? null,
                paymentMethod: $validated['payment_method'],
            );

            $order = $this->orderService->placeOrder(auth()->id(), $dto);

            return redirect()->route('checkout.success', ['order' => $order->public_id])
                ->with('success', 'Đặt hàng thành công!');

        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function success(string $order): View
    {
        $order = \App\Models\Order::where('public_id', $order)->firstOrFail();
        
        $order->load('items');

        // Store links
        $zaloLink = Setting::get('store.zalo_link');
        $messengerLink = Setting::get('store.messenger_link');
        
        // Generate QR code if payment method is qr_code or bank transfer
        $qrCode = null;
        if (in_array($order->payment_method, ['qr_code', 'bank_transfer'])) {
            $bankCode = Setting::get('bank.code', 'VCB');
            $accountNo = Setting::get('bank.account_number', '');
            $accountName = Setting::get('bank.account_name', '');
            $amount = $order->grand_total;
            $addInfo = 'DH' . str_pad((string) $order->id, 6, '0', STR_PAD_LEFT);
            
            $qrCode = $this->vietQRService->generateQR($bankCode, $accountNo, $amount, $addInfo, $accountName);
        }

        return view('front.checkout.success', compact('order', 'zaloLink', 'messengerLink', 'qrCode'));
    }
}
