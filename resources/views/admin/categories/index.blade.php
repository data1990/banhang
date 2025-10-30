@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-folder"></i> Danh mục sản phẩm</h4>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Thêm danh mục</a>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm theo tên danh mục">
            </div>
            <div class="col-md-2">
                <button class="btn btn-secondary w-100" type="submit"><i class="bi bi-search"></i> Lọc</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tên</th>
                    <th>Slug</th>
                    <th>Thuộc danh mục</th>
                    <th>Trạng thái</th>
                    <th>Thứ tự</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $cat)
                    <tr>
                        <td>{{ $cat->id }}</td>
                        <td>{{ $cat->name }}</td>
                        <td><code>{{ $cat->slug }}</code></td>
                        <td>{{ $cat->parent?->name ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $cat->is_active ? 'success' : 'secondary' }}">{{ $cat->is_active ? 'Hiển thị' : 'Ẩn' }}</span>
                        </td>
                        <td>{{ $cat->sort_order }}</td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.categories.edit', $cat) }}"><i class="bi bi-pencil"></i></a>
                            <form class="d-inline" method="POST" action="{{ route('admin.categories.destroy', $cat) }}" onsubmit="return confirm('Xóa danh mục này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Không có danh mục nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
        <div class="card-body">
            {{ $categories->links() }}
        </div>
    @endif
</div>
@endsection


