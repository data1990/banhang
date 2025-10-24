@extends('layouts.app')

@section('title', 'Thông tin cá nhân')

@section('content')
<div class="container py-4">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tài khoản</h5>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('account.dashboard') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('account.dashboard') ? 'active' : '' }}">
                            <i class="bi bi-speedometer2"></i> Tổng quan
                        </a>
                        <a href="{{ route('account.profile') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('account.profile') ? 'active' : '' }}">
                            <i class="bi bi-person"></i> Thông tin cá nhân
                        </a>
                        <a href="{{ route('account.orders') }}" 
                           class="list-group-item list-group-item-action {{ request()->routeIs('account.orders*') ? 'active' : '' }}">
                            <i class="bi bi-receipt"></i> Đơn hàng của tôi
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Thông tin cá nhân</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('account.profile.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Họ và tên *</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                       id="name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       id="email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label">Số điện thoại</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone', auth()->user()->phone) }}">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="password" class="form-label">Mật khẩu mới</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                       id="password" name="password">
                                <div class="form-text">Để trống nếu không muốn thay đổi</div>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu mới</label>
                            <input type="password" class="form-control" 
                                   id="password_confirmation" name="password_confirmation">
                        </div>

                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật thông tin
                            </button>
                            <a href="{{ route('account.dashboard') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left"></i> Quay lại
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Customer Profile -->
            @if(auth()->user()->profile)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('account.profile.shipping.update') }}">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="full_name" class="form-label">Họ và tên *</label>
                                    <input type="text" class="form-control @error('full_name') is-invalid @enderror" 
                                           id="full_name" name="full_name" 
                                           value="{{ old('full_name', auth()->user()->profile->full_name) }}" required>
                                    @error('full_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Số điện thoại *</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" 
                                           value="{{ old('phone', auth()->user()->profile->phone) }}" required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="address_line" class="form-label">Địa chỉ *</label>
                                <input type="text" class="form-control @error('address_line') is-invalid @enderror" 
                                       id="address_line" name="address_line" 
                                       value="{{ old('address_line', auth()->user()->profile->address_line) }}" required>
                                @error('address_line')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="ward" class="form-label">Phường/Xã *</label>
                                    <input type="text" class="form-control @error('ward') is-invalid @enderror" 
                                           id="ward" name="ward" 
                                           value="{{ old('ward', auth()->user()->profile->ward) }}" required>
                                    @error('ward')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="district" class="form-label">Quận/Huyện *</label>
                                    <input type="text" class="form-control @error('district') is-invalid @enderror" 
                                           id="district" name="district" 
                                           value="{{ old('district', auth()->user()->profile->district) }}" required>
                                    @error('district')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="province" class="form-label">Tỉnh/Thành phố *</label>
                                    <input type="text" class="form-control @error('province') is-invalid @enderror" 
                                           id="province" name="province" 
                                           value="{{ old('province', auth()->user()->profile->province) }}" required>
                                    @error('province')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label">Ghi chú</label>
                                <textarea class="form-control @error('note') is-invalid @enderror" 
                                          id="note" name="note" rows="3">{{ old('note', auth()->user()->profile->note) }}</textarea>
                                @error('note')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Cập nhật thông tin giao hàng
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="card mt-4">
                    <div class="card-body text-center">
                        <i class="bi bi-geo-alt text-muted" style="font-size: 3rem;"></i>
                        <h5 class="mt-3">Chưa có thông tin giao hàng</h5>
                        <p class="text-muted">Thông tin giao hàng sẽ được tự động tạo khi bạn đặt hàng lần đầu</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
