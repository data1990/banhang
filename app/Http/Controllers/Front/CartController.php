<?php

declare(strict_types=1);

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Cart\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    public function __construct(
        private CartService $cartService
    ) {}

    public function index(Request $request): View
    {
        $cart = $this->cartService->getOrCreateCart(
            auth()->id(),
            $request->session()->getId()
        );

        $cart->load(['items.product.images' => function ($query) {
            $query->primary()->ordered();
        }]);

        return view('front.cart.index', compact('cart'));
    }

    public function addItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:1',
        ]);

        try {
            $product = Product::findOrFail($request->product_id);
            $cart = $this->cartService->getOrCreateCart(
                auth()->id(),
                $request->session()->getId()
            );

            $this->cartService->addItem($cart, $product, $request->qty);

            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng',
                'cart_count' => $cart->fresh()->item_count,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function updateItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'qty' => 'required|integer|min:0',
        ]);

        try {
            $cart = $this->cartService->getOrCreateCart(
                auth()->id(),
                $request->session()->getId()
            );

            $this->cartService->updateItemQuantity($cart, $request->product_id, $request->qty);

            return response()->json([
                'success' => true,
                'message' => 'Đã cập nhật giỏ hàng',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function removeItem(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
        ]);

        try {
            $cart = $this->cartService->getOrCreateCart(
                auth()->id(),
                $request->session()->getId()
            );

            $this->cartService->removeItem($cart, $request->product_id);

            return response()->json([
                'success' => true,
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}
