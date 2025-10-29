@extends('layouts.admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0"><i class="bi bi-person"></i> Sửa tài khoản</h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Quay lại</a>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PATCH')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Họ tên *</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tài khoản (Username)</label>
                    <input type="text" name="username" value="{{ old('username', $user->username) }}" class="form-control @error('username') is-invalid @enderror" placeholder="Tùy chọn, dùng để đăng nhập">
                    <small class="text-muted">Có thể dùng username hoặc email để đăng nhập</small>
                    @error('username')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Điện thoại</label>
                    <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-control @error('phone') is-invalid @enderror">
                    @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Vai trò *</label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach($roles as $r)
                            <option value="{{ $r }}" {{ old('role', $user->role)===$r?'selected':'' }}>{{ strtoupper($r) }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Mật khẩu (để trống nếu không đổi)</label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="mt-3">
                <button class="btn btn-primary" type="submit"><i class="bi bi-check2"></i> Lưu</button>
            </div>
        </form>
    </div>
 </div>
@endsection


