@extends('layouts.app')

@section('title', 'Trang chủ')

@section('content')
<div class="container-fluid">
    <!-- Hero Section -->
    <div class="row bg-primary text-white py-5 mb-5">
        <div class="col-12 text-center">
            <h1 class="display-4 fw-bold">Chào mừng đến với {{ config('app.name', 'BanHang') }}</h1>
            <p class="lead">Khám phá những sản phẩm công nghệ mới nhất với giá tốt nhất</p>
            <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">Mua sắm ngay</a>
        </div>
    </div>

    <div class="container">
        <!-- New Products Section -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3">Sản phẩm mới</h2>
                <a href="{{ route('products.index', ['sort' => 'newest']) }}" class="btn btn-outline-primary">Xem tất cả</a>
            </div>
            
            @if($newProducts->count() > 0)
                <div class="row">
                    @foreach($newProducts as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card product-card h-100">
                                <div class="position-relative">
                                    @if($product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                             class="card-img-top" 
                                             alt="{{ $product->name }}"
                                             style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    
                                    @if($product->is_on_sale)
                                        <span class="badge-sale">SALE</span>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text text-muted small">{{ $product->short_desc }}</p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex align-items-center mb-3">
                                            @if($product->is_on_sale)
                                                <span class="price-sale me-2">{{ money_vnd($product->sale_price) }}</span>
                                                <span class="price-original small">{{ money_vnd($product->price) }}</span>
                                            @else
                                                <span class="price-sale">{{ money_vnd($product->price) }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('products.show', $product->slug) }}" 
                                               class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="bi bi-eye"></i> Xem chi tiết
                                            </a>
                                            <button class="btn btn-primary btn-sm add-to-cart" 
                                                    data-product-id="{{ $product->id }}">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">Chưa có sản phẩm mới</p>
                </div>
            @endif
        </section>

        <!-- Best Selling Products Section -->
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3">Sản phẩm bán chạy</h2>
                <a href="{{ route('products.index', ['sort' => 'best_selling']) }}" class="btn btn-outline-primary">Xem tất cả</a>
            </div>
            
            @if($bestSellingProducts->count() > 0)
                <div class="row">
                    @foreach($bestSellingProducts as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="card product-card h-100">
                                <div class="position-relative">
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" 
                                             class="card-img-top" 
                                             alt="{{ $product->name }}"
                                             style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 200px;">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text text-muted small">Đã bán: {{ number_format($product->total_qty) }} sản phẩm</p>
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex align-items-center mb-3">
                                            <span class="price-sale">{{ money_vnd($product->total_revenue / $product->total_qty) }}</span>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('products.show', $product->slug ?? '') }}" 
                                               class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="bi bi-eye"></i> Xem chi tiết
                                            </a>
                                            <button class="btn btn-primary btn-sm add-to-cart" 
                                                    data-product-id="{{ $product->id }}">
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-trophy text-muted" style="font-size: 4rem;"></i>
                    <p class="text-muted mt-3">Chưa có dữ liệu bán chạy</p>
                </div>
            @endif
        </section>

        <!-- Categories Section -->
        @if($categories->count() > 0)
        <section class="mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h3">Danh mục sản phẩm</h2>
                <a href="{{ route('products.index') }}" class="btn btn-outline-primary">Xem tất cả</a>
            </div>
            
            <div class="row">
                @foreach($categories as $category)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-grid text-primary" style="font-size: 3rem;"></i>
                                <h5 class="card-title mt-3">{{ $category->name }}</h5>
                                <p class="card-text text-muted">
                                    {{ $category->products->count() }} sản phẩm
                                </p>
                                <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                                   class="btn btn-outline-primary">
                                    Xem sản phẩm
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const qty = 1; // Default quantity
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                body: JSON.stringify({
                    product_id: productId,
                    qty: qty
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    const toast = document.createElement('div');
                    toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
                    toast.style.zIndex = '9999';
                    toast.innerHTML = `
                        <div class="d-flex">
                            <div class="toast-body">${data.message}</div>
                            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    new bootstrap.Toast(toast).show();
                    
                    // Update cart count
                    updateCartCount();
                } else {
                    alert(data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng');
            });
        });
    });
});
</script>
@endsection
