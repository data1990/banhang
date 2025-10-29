@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-people"></i> Quản lý tài khoản</h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-plus"></i> Tạo tài khoản</a>
    </div>

<div class="card mb-3">
    <div class="card-body">
        <form class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Tìm theo tên, tài khoản, email, số điện thoại">
            </div>
            <div class="col-md-3">
                <select name="role" class="form-select">
                    <option value="">-- Tất cả vai trò --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r }}" {{ request('role')===$r?'selected':'' }}>{{ strtoupper($r) }}</option>
                    @endforeach
                </select>
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
                    <th>Tài khoản</th>
                    <th>Email</th>
                    <th>Điện thoại</th>
                    <th>Vai trò</th>
                    <th class="text-end">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    <tr>
                        <td>{{ $user->id }}</td>
                        <td>{{ $user->name }}</td>
                        <td>{!! $user->username ?: '<span class="text-muted">-</span>' !!}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '-' }}</td>
                        <td>
                            <span class="badge bg-{{ $user->role==='admin'?'danger':($user->role==='kitchen'?'warning':($user->role==='shipper'?'info':($user->role==='staff'?'primary':'secondary'))) }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a class="btn btn-sm btn-outline-primary" href="{{ route('admin.users.edit',$user) }}"><i class="bi bi-pencil"></i></a>
                            @if(auth()->id() !== $user->id)
                            <form class="d-inline" method="POST" action="{{ route('admin.users.destroy',$user) }}" onsubmit="return confirm('Xóa tài khoản này?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" type="submit"><i class="bi bi-trash"></i></button>
                            </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">Không có tài khoản nào</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="card-body">
            {{ $users->links() }}
        </div>
    @endif
</div>
@endsection


