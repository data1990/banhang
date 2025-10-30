@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="text-center mb-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                <h1 class="display-4 text-success mt-3">Đặt hàng thành công!</h1>
                <p class="lead">Cảm ơn bạn đã mua sắm tại {{ config('app.name', 'BanHang') }}</p>
            </div>

            <!-- Order Information -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Thông tin đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Mã đơn hàng:</h6>
                            <p class="fw-bold text-primary">#{{ $order->short_id }}</p>
                            
                            <h6>Khách hàng:</h6>
                            <p>{{ $order->customer_name }}</p>
                            
                            <h6>Số điện thoại:</h6>
                            <p>{{ $order->customer_phone }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6>Thời gian nhận hàng:</h6>
                            <p>{{ $order->receive_at->format('d/m/Y H:i') }}</p>
                            
                            <h6>Phương thức thanh toán:</h6>
                            <p>{{ $order->payment_method_label }}</p>
                            
                            <h6>Tổng tiền:</h6>
                            <p class="fw-bold text-success h5">{{ money_vnd($order->grand_total) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Items -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Sản phẩm đã đặt</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th>Đơn giá</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="me-3">
                                                    <i class="bi bi-box text-muted" style="font-size: 2rem;"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-1">{{ $item->product_name }}</h6>
                                                    <small class="text-muted">SKU: {{ $item->sku }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $item->qty }}</td>
                                        <td>{{ money_vnd($item->unit_price) }}</td>
                                        <td class="fw-bold">{{ money_vnd($item->line_total) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Tổng cộng:</td>
                                    <td class="fw-bold text-success">{{ money_vnd($order->grand_total) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Payment Instructions -->
            @if(in_array($order->payment_method, ['qr_code', 'bank_transfer']) && $qrCode)
                <div class="card mb-4">
                    <div class="card-header bg-warning">
                        <h5 class="mb-0">
                            <i class="bi bi-qr-code"></i> Hướng dẫn thanh toán
                            @if($order->payment_method === 'qr_code')
                                - Quét mã QR
                            @else
                                - Chuyển khoản
                            @endif
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- QR Code Payment -->
                        <div class="row">
                            <div class="{{ $order->payment_method === 'qr_code' ? 'col-md-12' : 'col-md-6' }} text-center mb-3 mb-md-0">
                                <h6 class="mb-3">Quét mã QR để thanh toán</h6>
                                <img src="{{ $qrCode }}" alt="QR Code" class="img-fluid border rounded" style="max-width: 300px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                                <p class="text-muted mt-2 small">Quét bằng app ngân hàng để thanh toán nhanh</p>
                            </div>
                            @if($order->payment_method === 'bank_transfer')
                                <div class="col-md-6">
                                    <div class="alert alert-info">
                                        <h6>Hoặc chuyển khoản thủ công:</h6>
                                        <div class="mt-3">
                                            {!! str_replace('{ORDER_ID}', $order->short_id, \App\Models\Setting::get('bank.transfer_info', '')) !!}
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        @if($order->payment_method === 'bank_transfer')
                            <hr>
                        @endif
                        
                        <div class="bg-light p-3 rounded">
                            <strong>Nội dung chuyển khoản:</strong> 
                            <code>ORDER-{{ $order->short_id }}</code>
                        </div>
                        
                        <p class="text-muted mt-3">
                            <i class="bi bi-info-circle"></i>
                            Sau khi chuyển khoản, đơn hàng sẽ được xác nhận trong vòng 1-2 giờ làm việc.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Contact Information -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-headset"></i> Hỗ trợ khách hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Thông tin liên hệ:</h6>
                            <p>
                                <i class="bi bi-telephone"></i> 
                                {{ \App\Models\Setting::get('store.contact_phone', '0123456789') }}
                            </p>
                            <p>
                                <i class="bi bi-geo-alt"></i> 
                                {{ \App\Models\Setting::get('store.address', '123 Đường ABC, Quận 1, TP.HCM') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h6>Liên hệ trực tuyến:</h6>
                            <div class="d-flex gap-2">
                                @if($messengerLink)
                                    <a href="{{ $messengerLink }}" class="btn btn-primary btn-sm" target="_blank">
                                        <i class="bi bi-messenger"></i> Messenger
                                    </a>
                                @endif
                                @if($zaloLink)
                                    <a href="{{ $zaloLink }}" class="btn btn-success btn-sm" target="_blank">
                                        <i class="bi bi-chat-dots"></i> Zalo
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="text-center">
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="{{ route('account.orders') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-list-ul"></i> Xem đơn hàng của tôi
                    </a>
                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary btn-lg">
                        <i class="bi bi-shop"></i> Tiếp tục mua sắm
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to top
    window.scrollTo(0, 0);
    
    // Show success message if redirected from checkout
    @if(session('success'))
        const toast = document.createElement('div');
        toast.className = 'toast align-items-center text-white bg-success border-0 position-fixed top-0 end-0 m-3';
        toast.style.zIndex = '9999';
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">{{ session('success') }}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;
        document.body.appendChild(toast);
        new bootstrap.Toast(toast).show();
    @endif
});
</script>
@endsection
