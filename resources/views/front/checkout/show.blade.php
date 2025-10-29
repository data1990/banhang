@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Thanh toán</h2>
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            <form action="{{ route('checkout.place') }}" method="POST" id="checkout-form">
                @csrf
                <div class="row">
                    <!-- Order Summary -->
                    <div class="col-lg-4 mb-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Đơn hàng của bạn</h5>
                            </div>
                            <div class="card-body">
                                @foreach($cart->items as $item)
                                    <div class="d-flex align-items-center mb-3">
                                        @if($item->product->images->count() > 0)
                                            <img src="{{ asset('storage/' . $item->product->images->first()->path) }}" 
                                                 class="me-3" 
                                                 alt="{{ $item->product->name }}"
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        @else
                                            <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                                 style="width: 50px; height: 50px;">
                                                <i class="bi bi-image text-muted"></i>
                                            </div>
                                        @endif
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $item->product->name }}</h6>
                                            <small class="text-muted">x{{ $item->qty }}</small>
                                        </div>
                                        <span class="fw-bold">{{ money_vnd($item->line_total) }}</span>
                                    </div>
                                @endforeach
                                
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span>Tạm tính:</span>
                                    <span>{{ money_vnd($cart->total) }}</span>
                                </div>
                                <div class="d-flex justify-content-between">
                                    <span>Phí vận chuyển:</span>
                                    <span class="text-success">Miễn phí</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Tổng cộng:</span>
                                    <span class="text-primary">{{ money_vnd($cart->total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Checkout Form -->
                    <div class="col-lg-8">
                        <!-- Customer Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Thông tin khách hàng</h5>
                            </div>
                            <div class="card-body">
                                @guest
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i>
                                        Bạn chưa đăng nhập. 
                                        <a href="{{ route('login') }}" class="alert-link">Đăng nhập</a> 
                                        hoặc 
                                        <a href="{{ route('register') }}" class="alert-link">Đăng ký</a> 
                                        để lưu thông tin cho lần sau.
                                    </div>
                                @endguest

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_name" class="form-label">Họ và tên *</label>
                                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                                               id="customer_name" name="customer_name" 
                                               value="{{ old('customer_name', $profile->full_name ?? '') }}" required>
                                        @error('customer_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="customer_phone" class="form-label">Số điện thoại *</label>
                                        <input type="tel" class="form-control @error('customer_phone') is-invalid @enderror" 
                                               id="customer_phone" name="customer_phone" 
                                               value="{{ old('customer_phone', $profile->phone ?? '') }}" required>
                                        @error('customer_phone')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="customer_address" class="form-label">Địa chỉ nhận hàng *</label>
                                    <textarea class="form-control @error('customer_address') is-invalid @enderror" 
                                              id="customer_address" name="customer_address" rows="3" required>{{ old('customer_address', $profile->full_address ?? '') }}</textarea>
                                    @error('customer_address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    
                                    <!-- Map Address Picker Button -->
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-outline-primary btn-sm" id="pick-address-btn">
                                            <i class="bi bi-geo-alt"></i> Chọn vị trí trên bản đồ
                                        </button>
                                        <small class="text-muted d-block mt-1">
                                            Hoặc nhập địa chỉ thủ công vào ô trên
                                        </small>
                                    </div>
                                    
                                    <!-- Map Container (hidden by default) -->
                                    <div id="map-container" style="display: none; margin-top: 15px;">
                                        <div id="map" style="height: 300px; border-radius: 8px;"></div>
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-primary btn-sm" id="confirm-location-btn">
                                                <i class="bi bi-check-circle"></i> Xác nhận vị trí này
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm ms-2" id="cancel-map-btn">
                                                <i class="bi bi-x-circle"></i> Hủy
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Time -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Thời gian nhận hàng</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="receive_at" class="form-label">Chọn thời gian nhận hàng *</label>
                                    <input type="text" class="form-control @error('receive_at') is-invalid @enderror" 
                                           id="receive_at" name="receive_at" 
                                           value="{{ old('receive_at') }}" required>
                                    @error('receive_at')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        Thời gian nhận hàng phải sau ít nhất 2 giờ từ bây giờ
                                    </small>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Phương thức thanh toán</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" value="cod" 
                                                   {{ old('payment_method', 'cod') == 'cod' ? 'checked' : '' }}>
                                            <label class="form-check-label">
                                                <i class="bi bi-cash text-success"></i> <small>Thanh toán khi nhận hàng (COD)</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" 
                                                   id="payment_qr" value="qr_code" 
                                                   {{ old('payment_method') == 'qr_code' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="payment_qr">
                                                <i class="bi bi-qr-code text-info"></i> <small>Quét mã QR</small>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="payment_method" 
                                                   id="payment_bank" value="bank_transfer" 
                                                   {{ old('payment_method') == 'bank_transfer' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="payment_bank">
                                                <i class="bi bi-credit-card text-primary"></i> <small>Chuyển khoản</small>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- QR Code Info -->
                                <div id="qr-info" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <div class="d-flex align-items-center">
                                            <div>
                                                <i class="bi bi-qr-code-scan" style="font-size: 2rem;"></i>
                                            </div>
                                            <div class="ms-3">
                                                <h6 class="mb-1">Thanh toán bằng QR Code</h6>
                                                <p class="mb-0 small">Sau khi đặt hàng thành công, bạn sẽ nhận được mã QR để quét thanh toán</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Bank Transfer Info -->
                                <div id="bank-info" class="mt-3" style="display: none;">
                                    <div class="alert alert-info">
                                        <h6>Thông tin chuyển khoản:</h6>
                                        {!! $bankInfo !!}
                                        <hr>
                                        <strong>Nội dung chuyển khoản:</strong> ORDER-<span id="order-id-placeholder">XXXX</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5 class="mb-0">Ghi chú đơn hàng</h5>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="note" class="form-label">Ghi chú (không bắt buộc)</label>
                                    <textarea class="form-control @error('note') is-invalid @enderror" 
                                              id="note" name="note" rows="3" 
                                              placeholder="Ghi chú thêm cho đơn hàng...">{{ old('note') }}</textarea>
                                    @error('note')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle"></i> Đặt hàng
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- Google Maps API -->
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB41DRUbKWJHPxaFjMAwdrzWzbUZyR6Dpg&libraries=places&language=vi"></script>

<script>
let map;
let marker;
let geocoder;
let selectedAddress = '';
let autocomplete;

document.addEventListener('DOMContentLoaded', function() {
    // Initialize Flatpickr for delivery time
    flatpickr("#receive_at", {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: new Date().fp_incr(2), // 2 hours from now
        time_24hr: true,
        locale: "vn",
        minuteIncrement: 30,
        defaultDate: new Date().fp_incr(2)
    });

    // Payment method change handler
    document.querySelectorAll('input[name="payment_method"]').forEach(radio => {
        radio.addEventListener('change', function() {
            const bankInfo = document.getElementById('bank-info');
            const qrInfo = document.getElementById('qr-info');
            
            // Hide all info sections
            bankInfo.style.display = 'none';
            qrInfo.style.display = 'none';
            
            // Show relevant info based on payment method
            if (this.value === 'bank_transfer') {
                bankInfo.style.display = 'block';
            } else if (this.value === 'qr_code') {
                qrInfo.style.display = 'block';
            }
        });
    });

    // Trigger initial payment method check
    document.querySelector('input[name="payment_method"]:checked').dispatchEvent(new Event('change'));

    // Map initialization
    const pickAddressBtn = document.getElementById('pick-address-btn');
    const mapContainer = document.getElementById('map-container');
    const confirmLocationBtn = document.getElementById('confirm-location-btn');
    const cancelMapBtn = document.getElementById('cancel-map-btn');
    
    pickAddressBtn.addEventListener('click', function() {
        mapContainer.style.display = 'block';
        
        if (!map) {
            initMap();
        }
    });
    
    cancelMapBtn.addEventListener('click', function() {
        mapContainer.style.display = 'none';
        if (selectedAddress) {
            // Restore original address if user cancels
            document.getElementById('customer_address').value = document.getElementById('customer_address').value;
        }
    });
    
    confirmLocationBtn.addEventListener('click', function() {
        if (selectedAddress) {
            document.getElementById('customer_address').value = selectedAddress;
            mapContainer.style.display = 'none';
            
            // Show success message
            const toast = document.createElement('div');
            toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="d-flex">
                    <div class="toast-body">Đã chọn địa chỉ thành công</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            `;
            document.body.appendChild(toast);
            new bootstrap.Toast(toast).show();
            
            setTimeout(() => toast.remove(), 3000);
        } else {
            alert('Vui lòng chọn vị trí trên bản đồ');
        }
    });
    
    function initMap() {
        // Default center: Ho Chi Minh City, Vietnam
        const defaultCenter = { lat: 10.8231, lng: 106.6297 };
        
        map = new google.maps.Map(document.getElementById('map'), {
            zoom: 15,
            center: defaultCenter,
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true
        });
        
        geocoder = new google.maps.Geocoder();
        
        // Try to get user's current location
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    map.setCenter(pos);
                    addMarker(pos);
                    getAddressFromLatLng(pos);
                },
                function() {
                    // If location access denied, use default location
                    addMarker(defaultCenter);
                    getAddressFromLatLng(defaultCenter);
                }
            );
        } else {
            // Browser doesn't support Geolocation
            addMarker(defaultCenter);
            getAddressFromLatLng(defaultCenter);
        }
        
        // Add click listener to map
        map.addListener('click', function(event) {
            addMarker(event.latLng);
            getAddressFromLatLng(event.latLng);
        });
        
        // Add drag listener to marker
        map.addListener('dragend', function(event) {
            getAddressFromLatLng(marker.getPosition());
        });
    }
    
    function addMarker(position) {
        if (marker) {
            marker.setPosition(position);
        } else {
            marker = new google.maps.Marker({
                position: position,
                map: map,
                draggable: true,
                title: 'Địa chỉ giao hàng',
                animation: google.maps.Animation.DROP
            });
            
            // Add info window
            const infoWindow = new google.maps.InfoWindow({
                content: '<strong>Vị trí giao hàng</strong><br>Kéo marker để chọn vị trí chính xác'
            });
            
            marker.addListener('click', function() {
                infoWindow.open(map, marker);
            });
        }
    }
    
    function getAddressFromLatLng(latLng) {
        geocoder.geocode(
            { location: latLng },
            function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        selectedAddress = results[0].formatted_address;
                        confirmLocationBtn.disabled = false;
                        
                        // Show address preview
                        confirmLocationBtn.innerHTML = `
                            <i class="bi bi-check-circle"></i> Xác nhận: ${selectedAddress.substring(0, 40)}...
                        `;
                    } else {
                        selectedAddress = '';
                        confirmLocationBtn.disabled = true;
                        window.alert('Không tìm thấy địa chỉ');
                    }
                } else {
                    selectedAddress = '';
                    confirmLocationBtn.disabled = true;
                    window.alert('Lỗi khi tìm địa chỉ: ' + status);
                }
            }
        );
    }

    // Form submission
    document.getElementById('checkout-form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Đang xử lý...';
    });
});
</script>
@endsection
