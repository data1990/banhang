@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-12">
            <h2 class="mb-4">Giỏ hàng của bạn</h2>
            
            @if($cart->items->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th>Giá</th>
                                        <th>Số lượng</th>
                                        <th>Tổng</th>
                                        <th>Thao tác</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cart->items as $item)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($item->product->images->count() > 0)
                                                        <img src="{{ asset('storage/' . $item->product->images->first()->path) }}" 
                                                             class="me-3" 
                                                             alt="{{ $item->product->name }}"
                                                             style="width: 60px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="me-3 bg-light d-flex align-items-center justify-content-center" 
                                                             style="width: 60px; height: 60px;">
                                                            <i class="bi bi-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <h6 class="mb-1">{{ $item->product->name }}</h6>
                                                        <small class="text-muted">SKU: {{ $item->product->sku }}</small>
                                                        @if($item->product->is_on_sale)
                                                            <br><span class="badge bg-danger">SALE</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column">
                                                    @if($item->product->is_on_sale)
                                                        <span class="text-danger fw-bold">{{ money_vnd($item->product->sale_price) }}</span>
                                                        <small class="text-muted text-decoration-line-through">{{ money_vnd($item->product->price) }}</small>
                                                    @else
                                                        <span class="fw-bold">{{ money_vnd($item->product->price) }}</span>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group" style="width: 120px;">
                                                    <button class="btn btn-outline-secondary btn-sm update-qty" 
                                                            data-item-id="{{ $item->id }}" 
                                                            data-action="decrease">-</button>
                                                    <input type="number" class="form-control text-center" 
                                                           value="{{ $item->qty }}" 
                                                           min="1" 
                                                           max="{{ $item->product->stock }}"
                                                           data-item-id="{{ $item->id }}">
                                                    <button class="btn btn-outline-secondary btn-sm update-qty" 
                                                            data-item-id="{{ $item->id }}" 
                                                            data-action="increase">+</button>
                                                </div>
                                                <small class="text-muted">
                                                    Còn {{ $item->product->stock }} sản phẩm
                                                </small>
                                            </td>
                                            <td>
                                                <span class="fw-bold text-primary">{{ money_vnd($item->line_total) }}</span>
                                            </td>
                                            <td>
                                                <button class="btn btn-outline-danger btn-sm remove-item" 
                                                        data-item-id="{{ $item->id }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div class="row mt-4">
                    <div class="col-lg-8">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Mã giảm giá</h5>
                            </div>
                            <div class="card-body">
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Nhập mã giảm giá">
                                    <button class="btn btn-outline-primary" type="button">Áp dụng</button>
                                </div>
                                <small class="text-muted">Chưa có mã giảm giá nào</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Tổng kết đơn hàng</h5>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Tạm tính:</span>
                                    <span>{{ money_vnd($cart->total) }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Phí vận chuyển:</span>
                                    <span class="text-success">Miễn phí</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Giảm giá:</span>
                                    <span>0 đ</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between fw-bold">
                                    <span>Tổng cộng:</span>
                                    <span class="text-primary">{{ money_vnd($cart->total) }}</span>
                                </div>
                                
                                <div class="d-grid gap-2 mt-3">
                                    <a href="{{ route('checkout.show') }}" class="btn btn-primary btn-lg">
                                        <i class="bi bi-credit-card"></i> Thanh toán
                                    </a>
                                    <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                                        <i class="bi bi-arrow-left"></i> Tiếp tục mua sắm
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart -->
                <div class="text-center py-5">
                    <i class="bi bi-cart-x text-muted" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mt-3">Giỏ hàng trống</h4>
                    <p class="text-muted">Bạn chưa có sản phẩm nào trong giỏ hàng</p>
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-shop"></i> Mua sắm ngay
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Update quantity
    document.querySelectorAll('.update-qty').forEach(button => {
        button.addEventListener('click', function() {
            const itemId = this.dataset.itemId;
            const action = this.dataset.action;
            const qtyInput = document.querySelector(`input[data-item-id="${itemId}"]`);
            const currentQty = parseInt(qtyInput.value);
            const maxQty = parseInt(qtyInput.max);
            
            let newQty = currentQty;
            if (action === 'increase' && currentQty < maxQty) {
                newQty = currentQty + 1;
            } else if (action === 'decrease' && currentQty > 1) {
                newQty = currentQty - 1;
            }
            
            if (newQty !== currentQty) {
                updateCartItem(itemId, newQty);
            }
        });
    });

    // Direct quantity input
    document.querySelectorAll('input[data-item-id]').forEach(input => {
        input.addEventListener('change', function() {
            const itemId = this.dataset.itemId;
            const newQty = parseInt(this.value);
            const maxQty = parseInt(this.max);
            
            if (newQty < 1) {
                this.value = 1;
                return;
            }
            
            if (newQty > maxQty) {
                this.value = maxQty;
                alert('Không đủ hàng trong kho');
                return;
            }
            
            updateCartItem(itemId, newQty);
        });
    });

    // Remove item
    document.querySelectorAll('.remove-item').forEach(button => {
        button.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) {
                const itemId = this.dataset.itemId;
                removeCartItem(itemId);
            }
        });
    });

    function updateCartItem(itemId, qty) {
        fetch('{{ route("cart.update") }}', {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            },
            body: JSON.stringify({
                product_id: itemId,
                qty: qty
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // Reload to update totals
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật giỏ hàng');
        });
    }

    function removeCartItem(itemId) {
        fetch('{{ route("cart.remove") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.Laravel.csrfToken
            },
            body: JSON.stringify({
                product_id: itemId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa sản phẩm');
        });
    }
});
</script>
@endsection
