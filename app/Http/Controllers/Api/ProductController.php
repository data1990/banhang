<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function search(Request $request): JsonResponse
    {
        $query = Product::active()->inStock();

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('short_desc', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->with(['images' => function ($query) {
            $query->primary()->ordered();
        }])
        ->limit(20)
        ->get();

        return response()->json([
            'success' => true,
            'data' => $products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'sku' => $product->sku,
                    'price' => $product->price,
                    'sale_price' => $product->sale_price,
                    'current_price' => $product->current_price,
                    'is_on_sale' => $product->is_on_sale,
                    'stock' => $product->stock,
                    'image' => $product->images->first()?->path,
                ];
            }),
        ]);
    }
}
