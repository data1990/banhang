<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\Media\ImageService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProductsController extends Controller
{
    public function __construct(
        private ImageService $imageService
    ) {}

    public function index(Request $request): View
    {
        $query = Product::with(['category', 'images' => function ($query) {
            $query->primary()->ordered();
        }]);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // Filter by status
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $products = $query->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::active()->ordered()->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create(): View
    {
        $categories = Category::active()->ordered()->get();
        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = Product::create($request->validated());

        // Handle images
        if ($request->hasFile('images')) {
            $this->imageService->uploadProductImages($product, $request->file('images'));
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được tạo thành công');
    }

    public function show(Product $product): View
    {
        $product->load(['category', 'images' => function ($query) {
            $query->ordered();
        }]);

        return view('admin.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load(['images' => function ($query) {
            $query->ordered();
        }]);
        
        $categories = Category::active()->ordered()->get();
        
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $product->update($request->validated());

        // Handle new images
        if ($request->hasFile('images')) {
            $this->imageService->uploadProductImages($product, $request->file('images'));
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được cập nhật thành công');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        
        return redirect()->route('admin.products.index')
            ->with('success', 'Sản phẩm đã được xóa thành công');
    }

    public function toggleActive(Product $product): RedirectResponse
    {
        $product->update(['is_active' => !$product->is_active]);
        
        $status = $product->is_active ? 'kích hoạt' : 'vô hiệu hóa';
        
        return back()->with('success', "Sản phẩm đã được {$status}");
    }

    public function deleteImage(ProductImage $image): RedirectResponse
    {
        $this->imageService->deleteImage($image);
        
        return back()->with('success', 'Ảnh đã được xóa');
    }

    public function setPrimaryImage(ProductImage $image): RedirectResponse
    {
        // Remove primary from other images
        $image->product->images()->update(['is_primary' => false]);
        
        // Set this image as primary
        $image->update(['is_primary' => true]);
        
        return back()->with('success', 'Ảnh chính đã được cập nhật');
    }
}
