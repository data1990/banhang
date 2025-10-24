@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
            @if($product->category)
                <li class="breadcrumb-item">
                    <a href="{{ route('products.index', ['category' => $product->category->id]) }}">
                        {{ $product->category->name }}
                    </a>
                </li>
            @endif
            <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Images -->
        <div class="col-lg-6 mb-4">
            @if($product->images->count() > 0)
                <!-- Main Image -->
                <div class="mb-3">
                    <img id="main-image" 
                         src="{{ product_image_url($product->images->first()->path, 'large') }}" 
                         class="img-fluid rounded" 
                         alt="{{ $product->name }}"
                         style="max-height: 500px; object-fit: cover;">
                </div>

                <!-- Thumbnail Images -->
                @if($product->images->count() > 1)
                    <div class="row">
                        @foreach($product->images as $index => $image)
                            <div class="col-3 mb-2">
                                <img src="{{ product_thumbnail_url($image->path) }}" 
                                     class="img-thumbnail cursor-pointer thumbnail-image" 
                                     alt="{{ $product->name }}"
                                     data-main-src="{{ product_image_url($image->path, 'large') }}"
                                     style="height: 80px; object-fit: cover;">
                            </div>
                        @endforeach
                    </div>
                @endif
            @else
                <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                     style="height: 500px;">
                    <i class="bi bi-image text-muted" style="font-size: 4rem;"></i>
                </div>
            @endif
        </div>

        <!-- Product Info -->
        <div class="col-lg-6">
            <div class="card">
                <div class="card-body">
                    <h1 class="h3 mb-3">{{ $product->name }}</h1>
                    
                    <!-- SKU -->
                    <p class="text-muted mb-3">
                        <i class="bi bi-tag"></i> Mã sản phẩm: {{ $product->sku }}
                    </p>

                    <!-- Price -->
                    <div class="mb-4">
                        @if($product->is_on_sale)
                            <div class="d-flex align-items-center">
                                <span class="h4 text-danger me-3">{{ money_vnd($product->sale_price) }}</span>
                                <span class="text-muted text-decoration-line-through">{{ money_vnd($product->price) }}</span>
                                <span class="badge bg-danger ms-2">SALE</span>
                            </div>
                        @else
                            <span class="h4 text-primary">{{ money_vnd($product->price) }}</span>
                        @endif
                    </div>

                    <!-- Stock Status -->
                    <div class="mb-4">
                        @if($product->stock > 0)
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Còn hàng ({{ $product->stock }} sản phẩm)
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle"></i> Hết hàng
                            </span>
                        @endif
                    </div>

                    <!-- Short Description -->
                    @if($product->short_desc)
                        <p class="text-muted mb-4">{{ $product->short_desc }}</p>
                    @endif

                    <!-- Add to Cart Form -->
                    <form id="add-to-cart-form" class="mb-4">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="row align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="form-label">Số lượng:</label>
                            </div>
                            <div class="col-auto">
                                <div class="input-group" style="width: 120px;">
                                    <button class="btn btn-outline-secondary" type="button" id="decrease-qty">-</button>
                                    <input type="number" class="form-control text-center" id="quantity" name="qty" 
                                           value="1" min="1" max="{{ $product->stock }}">
                                    <button class="btn btn-outline-secondary" type="button" id="increase-qty">+</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Action Buttons -->
                    <div class="d-grid gap-2 d-md-flex">
                        <button type="button" class="btn btn-primary btn-lg add-to-cart" 
                                {{ $product->stock <= 0 ? 'disabled' : '' }}>
                            <i class="bi bi-cart-plus"></i> 
                            {{ $product->stock <= 0 ? 'Hết hàng' : 'Thêm vào giỏ' }}
                        </button>
                        <button type="button" class="btn btn-outline-primary btn-lg" 
                                data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                            <i class="bi bi-cart3"></i> Xem giỏ hàng
                        </button>
                    </div>

                    <!-- Product Details -->
                    @if($product->long_desc)
                        <div class="mt-5">
                            <h5>Mô tả sản phẩm</h5>
                            <div class="text-muted">
                                {!! nl2br(e($product->long_desc)) !!}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->count() > 0)
        <div class="mt-5">
            <h3 class="mb-4">Sản phẩm liên quan</h3>
            <div class="row">
                @foreach($relatedProducts as $relatedProduct)
                    <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                        <div class="card product-card h-100">
                            <div class="position-relative">
                                @if($relatedProduct->images->count() > 0)
                                    <img src="{{ asset('storage/' . $relatedProduct->images->first()->path) }}" 
                                         class="card-img-top" 
                                         alt="{{ $relatedProduct->name }}"
                                         style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                         style="height: 200px;">
                                        <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                    </div>
                                @endif
                                
                                @if($relatedProduct->is_on_sale)
                                    <span class="badge-sale">SALE</span>
                                @endif
                            </div>
                            
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title">{{ $relatedProduct->name }}</h5>
                                
                                <div class="mt-auto">
                                    <div class="d-flex align-items-center mb-3">
                                        @if($relatedProduct->is_on_sale)
                                            <span class="price-sale me-2">{{ money_vnd($relatedProduct->sale_price) }}</span>
                                            <span class="price-original small">{{ money_vnd($relatedProduct->price) }}</span>
                                        @else
                                            <span class="price-sale">{{ money_vnd($relatedProduct->price) }}</span>
                                        @endif
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('products.show', $relatedProduct->slug) }}" 
                                           class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="bi bi-eye"></i> Xem
                                        </a>
                                        <button class="btn btn-primary btn-sm add-to-cart" 
                                                data-product-id="{{ $relatedProduct->id }}"
                                                {{ $relatedProduct->stock <= 0 ? 'disabled' : '' }}>
                                            <i class="bi bi-cart-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Thumbnail image click handler
    document.querySelectorAll('.thumbnail-image').forEach(thumb => {
        thumb.addEventListener('click', function() {
            const mainImage = document.getElementById('main-image');
            mainImage.src = this.dataset.mainSrc;
        });
    });

    // Quantity controls
    document.getElementById('decrease-qty').addEventListener('click', function() {
        const qtyInput = document.getElementById('quantity');
        const currentValue = parseInt(qtyInput.value);
        if (currentValue > 1) {
            qtyInput.value = currentValue - 1;
        }
    });

    document.getElementById('increase-qty').addEventListener('click', function() {
        const qtyInput = document.getElementById('quantity');
        const maxValue = parseInt(qtyInput.max);
        const currentValue = parseInt(qtyInput.value);
        if (currentValue < maxValue) {
            qtyInput.value = currentValue + 1;
        }
    });

    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            if (this.disabled) {
                alert('Sản phẩm đã hết hàng');
                return;
            }

            const productId = this.dataset.productId || {{ $product->id }};
            const qty = this.dataset.productId ? 1 : document.getElementById('quantity').value;
            
            fetch('{{ route("cart.add") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                },
                body: JSON.stringify({
                    product_id: productId,
                    qty: parseInt(qty)
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    updateCartCount();
                } else {
                    showToast(data.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Có lỗi xảy ra khi thêm sản phẩm vào giỏ hàng', 'error');
            });
        });
    });

    function showToast(message, type) {
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0 position-fixed top-0 end-0 m-3`;
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        new bootstrap.Toast(toast).show();
    }
});
</script>
@endsection
