@extends('layouts.admin')

@section('title', 'Chi tiết sản phẩm: ' . $product->name)
@section('page-title', 'Chi tiết sản phẩm')

@section('content')
<div class="row">
    <!-- Product Information -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Thông tin sản phẩm</h5>
                <div>
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning btn-sm">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Tên sản phẩm:</h6>
                        <p class="text-muted">{{ $product->name }}</p>
                        
                        <h6>Slug:</h6>
                        <p class="text-muted"><code>{{ $product->slug }}</code></p>
                        
                        <h6>Mã SKU:</h6>
                        <p class="text-muted"><code>{{ $product->sku }}</code></p>
                        
                        <h6>Danh mục:</h6>
                        <p class="text-muted">
                            @if($product->category)
                                <span class="badge bg-secondary">{{ $product->category->name }}</span>
                            @else
                                <span class="text-muted">Chưa phân loại</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Giá gốc:</h6>
                        <p class="text-muted">{{ money_vnd($product->price) }}</p>
                        
                        @if($product->is_on_sale)
                            <h6>Giá khuyến mãi:</h6>
                            <p class="text-danger fw-bold">{{ money_vnd($product->sale_price) }}</p>
                        @endif
                        
                        <h6>Số lượng tồn kho:</h6>
                        <p class="text-muted">
                            <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                {{ $product->stock }}
                            </span>
                        </p>
                        
                        <h6>Trạng thái:</h6>
                        <p class="text-muted">
                            <span class="badge {{ $product->is_active ? 'bg-success' : 'bg-secondary' }}">
                                {{ $product->is_active ? 'Đang bán' : 'Ngừng bán' }}
                            </span>
                        </p>
                    </div>
                </div>
                
                @if($product->short_desc)
                    <hr>
                    <h6>Mô tả ngắn:</h6>
                    <p class="text-muted">{{ $product->short_desc }}</p>
                @endif
                
                @if($product->long_desc)
                    <hr>
                    <h6>Mô tả chi tiết:</h6>
                    <div class="text-muted">
                        {!! nl2br(e($product->long_desc)) !!}
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Images -->
        @if($product->images->count() > 0)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Hình ảnh sản phẩm</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @foreach($product->images as $image)
                        <div class="col-md-3 mb-3">
                            <div class="position-relative">
                                <img src="{{ asset('storage/' . $image->path) }}" 
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
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Product Stats -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thống kê sản phẩm</h5>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Ngày tạo:</span>
                    <span>{{ $product->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Cập nhật lần cuối:</span>
                    <span>{{ $product->updated_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Số lượng đã bán:</span>
                    <span class="text-success">{{ $product->orderItems->sum('qty') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Doanh thu:</span>
                    <span class="text-primary fw-bold">{{ money_vnd($product->orderItems->sum('line_total')) }}</span>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Thao tác</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                        <i class="bi bi-pencil"></i> Chỉnh sửa
                    </a>
                    
                    <form method="POST" action="{{ route('admin.products.toggle-active', $product) }}" class="d-inline">
                        @csrf
                        <button type="submit" 
                                class="btn {{ $product->is_active ? 'btn-secondary' : 'btn-success' }} w-100"
                                onclick="return confirm('Bạn có chắc muốn thay đổi trạng thái sản phẩm?')">
                            <i class="bi bi-{{ $product->is_active ? 'pause' : 'play' }}"></i> 
                            {{ $product->is_active ? 'Ngừng bán' : 'Kích hoạt' }}
                        </button>
                    </form>
                    
                    <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                          class="d-inline"
                          onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-trash"></i> Xóa sản phẩm
                        </button>
                    </form>
                    
                    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Quay lại danh sách
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
                        <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                             class="img-fluid mb-3" 
                             alt="{{ $product->name }}"
                             style="max-height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-light d-flex align-items-center justify-content-center mb-3" 
                             style="height: 200px;">
                            <i class="bi bi-image text-muted" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    
                    <h6>{{ $product->name }}</h6>
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        @if($product->is_on_sale)
                            <span class="text-danger fw-bold me-2">{{ money_vnd($product->sale_price) }}</span>
                            <span class="text-muted text-decoration-line-through small">{{ money_vnd($product->price) }}</span>
                        @else
                            <span class="text-primary fw-bold">{{ money_vnd($product->price) }}</span>
                        @endif
                    </div>
                    <small class="text-muted">Tồn kho: {{ $product->stock }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
