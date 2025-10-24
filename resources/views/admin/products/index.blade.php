@extends('layouts.admin')

@section('title', 'Quản lý sản phẩm')
@section('page-title', 'Quản lý sản phẩm')

@section('content')
<!-- Action Buttons -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Thêm sản phẩm mới
        </a>
    </div>
    <div>
        <span class="text-muted">Tổng: {{ $products->total() }} sản phẩm</span>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.products.index') }}">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="search" class="form-label">Tìm kiếm</label>
                    <input type="text" class="form-control" id="search" name="search" 
                           value="{{ request('search') }}" placeholder="Tên sản phẩm, SKU...">
                </div>
                <div class="col-md-3 mb-3">
                    <label for="category_id" class="form-label">Danh mục</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">Tất cả danh mục</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" 
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label for="is_active" class="form-label">Trạng thái</label>
                    <select class="form-select" id="is_active" name="is_active">
                        <option value="">Tất cả</option>
                        <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Đang bán</option>
                        <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Ngừng bán</option>
                    </select>
                </div>
                <div class="col-md-2 mb-3">
                    <label class="form-label">&nbsp;</label>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary">Lọc</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Products Table -->
<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Hình ảnh</th>
                            <th>Sản phẩm</th>
                            <th>SKU</th>
                            <th>Danh mục</th>
                            <th>Giá</th>
                            <th>Tồn kho</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>
                                    @if($product->images->count() > 0)
                                        <img src="{{ asset('storage/' . $product->images->first()->path) }}" 
                                             alt="{{ $product->name }}"
                                             style="width: 50px; height: 50px; object-fit: cover;"
                                             class="rounded">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center rounded" 
                                             style="width: 50px; height: 50px;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div>
                                        <h6 class="mb-1">{{ $product->name }}</h6>
                                        <small class="text-muted">{{ Str::limit($product->short_desc, 50) }}</small>
                                    </div>
                                </td>
                                <td>
                                    <code>{{ $product->sku }}</code>
                                </td>
                                <td>
                                    @if($product->category)
                                        <span class="badge bg-secondary">{{ $product->category->name }}</span>
                                    @else
                                        <span class="text-muted">Chưa phân loại</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        @if($product->is_on_sale)
                                            <span class="text-danger fw-bold">{{ money_vnd($product->sale_price) }}</span>
                                            <small class="text-muted text-decoration-line-through">{{ money_vnd($product->price) }}</small>
                                        @else
                                            <span class="fw-bold">{{ money_vnd($product->price) }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ route('admin.products.toggle-active', $product) }}" 
                                          class="d-inline">
                                        @csrf
                                        <button type="submit" 
                                                class="btn btn-sm {{ $product->is_active ? 'btn-success' : 'btn-secondary' }}"
                                                onclick="return confirm('Bạn có chắc muốn thay đổi trạng thái sản phẩm?')">
                                            {{ $product->is_active ? 'Đang bán' : 'Ngừng bán' }}
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.products.show', $product) }}" 
                                           class="btn btn-sm btn-outline-primary" 
                                           data-bs-toggle="tooltip" title="Xem chi tiết">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" 
                                           class="btn btn-sm btn-outline-warning" 
                                           data-bs-toggle="tooltip" title="Chỉnh sửa">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.products.destroy', $product) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    data-bs-toggle="tooltip" title="Xóa">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-box text-muted" style="font-size: 4rem;"></i>
                <h4 class="text-muted mt-3">Chưa có sản phẩm nào</h4>
                <p class="text-muted">Hãy thêm sản phẩm đầu tiên của bạn</p>
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Thêm sản phẩm
                </a>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endsection
