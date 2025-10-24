@extends('layouts.app')

@section('title', 'Sản phẩm')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar Filters -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Bộ lọc</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('products.index') }}">
                        <!-- Search -->
                        <div class="mb-3">
                            <label for="search" class="form-label">Tìm kiếm</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="Nhập tên sản phẩm...">
                        </div>

                        <!-- Category Filter -->
                        <div class="mb-3">
                            <label for="category" class="form-label">Danh mục</label>
                            <select class="form-select" id="category" name="category">
                                <option value="">Tất cả danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">Khoảng giá</label>
                            <div class="row">
                                <div class="col-6">
                                    <input type="number" class="form-control" name="min_price" 
                                           value="{{ request('min_price') }}" placeholder="Từ" min="0">
                                </div>
                                <div class="col-6">
                                    <input type="number" class="form-control" name="max_price" 
                                           value="{{ request('max_price') }}" placeholder="Đến" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Sort -->
                        <div class="mb-3">
                            <label for="sort" class="form-label">Sắp xếp</label>
                            <select class="form-select" id="sort" name="sort">
                                <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                                <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                                <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Tên A-Z</option>
                            </select>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Lọc</button>
                            <a href="{{ route('products.index') }}" class="btn btn-outline-secondary">Xóa bộ lọc</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <!-- Results Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 mb-0">
                    Sản phẩm 
                    @if(request('search'))
                        cho "{{ request('search') }}"
                    @endif
                    <small class="text-muted">({{ $products->total() }} sản phẩm)</small>
                </h2>
            </div>

            @if($products->count() > 0)
                <div class="row">
                    @foreach($products as $product)
                        <div class="col-lg-4 col-md-6 mb-4">
                            <div class="card product-card h-100">
                                <div class="position-relative">
                                    @if($product->images->count() > 0)
                                        <img src="{{ product_image_url($product->images->first()->path, 'small') }}" 
                                             class="card-img-top" 
                                             alt="{{ $product->name }}"
                                             style="height: 250px; object-fit: cover;">
                                    @else
                                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" 
                                             style="height: 250px;">
                                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                                        </div>
                                    @endif
                                    
                                    @if($product->is_on_sale)
                                        <span class="badge-sale">SALE</span>
                                    @endif
                                </div>
                                
                                <div class="card-body d-flex flex-column">
                                    <h5 class="card-title">{{ $product->name }}</h5>
                                    <p class="card-text text-muted small">{{ Str::limit($product->short_desc, 100) }}</p>
                                    
                                    @if($product->category)
                                        <small class="text-muted">
                                            <i class="bi bi-tag"></i> {{ $product->category->name }}
                                        </small>
                                    @endif
                                    
                                    <div class="mt-auto">
                                        <div class="d-flex align-items-center mb-3">
                                            @if($product->is_on_sale)
                                                <span class="price-sale me-2">{{ money_vnd($product->sale_price) }}</span>
                                                <span class="price-original small">{{ money_vnd($product->price) }}</span>
                                            @else
                                                <span class="price-sale">{{ money_vnd($product->price) }}</span>
                                            @endif
                                        </div>
                                        
                                        <div class="d-flex align-items-center mb-3">
                                            <small class="text-muted">
                                                <i class="bi bi-box"></i> Còn {{ $product->stock }} sản phẩm
                                            </small>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <a href="{{ route('products.show', $product->slug) }}" 
                                               class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="bi bi-eye"></i> Chi tiết
                                            </a>
                                            <button class="btn btn-primary btn-sm add-to-cart" 
                                                    data-product-id="{{ $product->id }}"
                                                    {{ $product->stock <= 0 ? 'disabled' : '' }}>
                                                <i class="bi bi-cart-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-search text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">Không tìm thấy sản phẩm</h4>
                    <p class="text-muted">Hãy thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary">Xem tất cả sản phẩm</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add to cart functionality
    document.querySelectorAll('.add-to-cart').forEach(button => {
        button.addEventListener('click', function() {
            if (this.disabled) {
                alert('Sản phẩm đã hết hàng');
                return;
            }

            const productId = this.dataset.productId;
            const qty = 1;
            
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
                    // Show success toast
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
