@extends('layouts.admin')

@section('title', 'Chỉnh sửa sản phẩm')
@section('page-title', 'Chỉnh sửa sản phẩm')

@section('content')
<form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="row">
        <!-- Product Information -->
        <div class="col-lg-8">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin sản phẩm</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Tên sản phẩm *</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="slug" class="form-label">Slug *</label>
                            <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                   id="slug" name="slug" value="{{ old('slug', $product->slug) }}" required>
                            @error('slug')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="sku" class="form-label">Mã SKU *</label>
                            <input type="text" class="form-control @error('sku') is-invalid @enderror" 
                                   id="sku" name="sku" value="{{ old('sku', $product->sku) }}" required>
                            @error('sku')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="category_id" class="form-label">Danh mục</label>
                            <select class="form-select @error('category_id') is-invalid @enderror" 
                                    id="category_id" name="category_id">
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Giá gốc (VND) *</label>
                            <input type="number" class="form-control @error('price') is-invalid @enderror" 
                                   id="price" name="price" value="{{ old('price', $product->price) }}" min="0" required>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="sale_price" class="form-label">Giá khuyến mãi (VND)</label>
                            <input type="number" class="form-control @error('sale_price') is-invalid @enderror" 
                                   id="sale_price" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" min="0">
                            @error('sale_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label">Số lượng tồn kho *</label>
                            <input type="number" class="form-control @error('stock') is-invalid @enderror" 
                                   id="stock" name="stock" value="{{ old('stock', $product->stock) }}" min="0" required>
                            @error('stock')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="short_desc" class="form-label">Mô tả ngắn</label>
                        <textarea class="form-control @error('short_desc') is-invalid @enderror" 
                                  id="short_desc" name="short_desc" rows="3">{{ old('short_desc', $product->short_desc) }}</textarea>
                        @error('short_desc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="long_desc" class="form-label">Mô tả chi tiết</label>
                        <textarea class="form-control @error('long_desc') is-invalid @enderror" 
                                  id="long_desc" name="long_desc" rows="5">{{ old('long_desc', $product->long_desc) }}</textarea>
                        @error('long_desc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                               value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">
                            Sản phẩm đang bán
                        </label>
                    </div>
                </div>
            </div>

            <!-- Current Images -->
            @if($product->images->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Hình ảnh hiện tại</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($product->images as $image)
                            <div class="col-md-3 mb-3">
                                <div class="position-relative">
                                    <img src="{{ product_thumbnail_url($image->path) }}" 
                                         class="img-thumbnail" 
                                         alt="{{ $product->name }}"
                                         style="width: 100%; height: 150px; object-fit: cover;">
                                    
                                    @if($image->is_primary)
                                        <span class="badge bg-primary position-absolute top-0 start-0">Ảnh chính</span>
                                    @endif
                                    
                                    <div class="mt-2 text-center">
                                        <form method="POST" action="{{ route('admin.products.images.primary', $image) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-star"></i> Đặt làm ảnh chính
                                            </button>
                                        </form>
                                        
                                        <form method="POST" action="{{ route('admin.products.images.delete', $image) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa ảnh này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            <!-- Add New Images -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thêm hình ảnh mới</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="images" class="form-label">Chọn hình ảnh</label>
                        <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                               id="images" name="images[]" multiple accept="image/*">
                        <div class="form-text">Có thể chọn nhiều hình ảnh cùng lúc.</div>
                        @error('images.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Image Preview -->
                    <div id="image-preview" class="row"></div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Save Actions -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thao tác</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Cập nhật sản phẩm
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>

            <!-- Product Preview -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Xem trước</h5>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        @if($product->images->count() > 0)
                            <img src="{{ product_image_url($product->images->first()->path, 'medium') }}" 
                                 class="img-fluid mb-3" 
                                 alt="{{ $product->name }}"
                                 style="max-height: 200px; object-fit: cover;">
                        @else
                            <div id="preview-image" class="bg-light d-flex align-items-center justify-content-center mb-3" 
                                 style="height: 200px;">
                                <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                            </div>
                        @endif
                        <h6 id="preview-name">{{ $product->name }}</h6>
                        <div id="preview-price" class="text-primary fw-bold">{{ money_vnd($product->current_price) }}</div>
                        <small id="preview-stock" class="text-muted">Tồn kho: {{ $product->stock }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug from name
    const nameInput = document.getElementById('name');
    const slugInput = document.getElementById('slug');
    
    nameInput.addEventListener('input', function() {
        const slug = this.value
            .toLowerCase()
            .normalize('NFD')
            .replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9\s-]/g, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .trim('-');
        slugInput.value = slug;
        
        // Update preview
        updatePreview();
    });

    // Update preview when inputs change
    document.getElementById('price').addEventListener('input', updatePreview);
    document.getElementById('sale_price').addEventListener('input', updatePreview);
    document.getElementById('stock').addEventListener('input', updatePreview);

    // Image preview
    document.getElementById('images').addEventListener('change', function(e) {
        const files = Array.from(e.target.files);
        const preview = document.getElementById('image-preview');
        preview.innerHTML = '';

        files.forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const col = document.createElement('div');
                    col.className = 'col-md-6 mb-2';
                    col.innerHTML = `
                        <div class="position-relative">
                            <img src="${e.target.result}" class="img-thumbnail" style="width: 100%; height: 100px; object-fit: cover;">
                        </div>
                    `;
                    preview.appendChild(col);
                };
                reader.readAsDataURL(file);
            }
        });
    });

    function updatePreview() {
        const name = document.getElementById('name').value || '{{ $product->name }}';
        const price = parseInt(document.getElementById('price').value) || {{ $product->price }};
        const salePrice = parseInt(document.getElementById('sale_price').value) || 0;
        const stock = parseInt(document.getElementById('stock').value) || {{ $product->stock }};

        document.getElementById('preview-name').textContent = name;
        document.getElementById('preview-stock').textContent = `Tồn kho: ${stock}`;

        if (salePrice > 0 && salePrice < price) {
            document.getElementById('preview-price').innerHTML = `
                <span class="text-danger">${formatMoney(salePrice)}</span>
                <small class="text-muted text-decoration-line-through ms-2">${formatMoney(price)}</small>
            `;
        } else {
            document.getElementById('preview-price').textContent = formatMoney(price);
        }
    }

    function formatMoney(amount) {
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }
});
</script>
@endsection
