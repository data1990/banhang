<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\DTOs\CheckoutDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use App\Models\Setting;
use App\Services\Cart\CartService;
use App\Services\Orders\OrderService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(
        private CartService $cartService,
        private OrderService $orderService
    ) {}

    public function show(Request $request): View
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
            $dto = CheckoutDTO::fromArray($request->validated());
            $dto = new CheckoutDTO(
                userId: auth()->id(),
                sessionToken: $request->session()->getId(),
                customerName: $dto->customerName,
                customerPhone: $dto->customerPhone,
                customerAddress: $dto->customerAddress,
                receiveAt: $dto->receiveAt,
                note: $dto->note,
                paymentMethod: $dto->paymentMethod,
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

        return view('front.checkout.success', compact('order', 'zaloLink', 'messengerLink'));
    }
}
