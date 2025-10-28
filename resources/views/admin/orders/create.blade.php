@extends('layouts.admin')

@section('title', 'Tạo đơn hàng mới')
@section('page-title', 'Tạo đơn hàng mới')

@section('content')
<form action="{{ route('admin.orders.store') }}" method="POST" id="create-order-form">
    @csrf
    
    <div class="row">
        <!-- Customer Information -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-person-circle"></i> Thông tin khách hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="customer_select" class="form-label fw-bold">
                            <i class="bi bi-person-check"></i> Chọn khách hàng đã từng đặt hàng
                        </label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" 
                                id="customer_select">
                            <option value="" selected disabled>Tìm kiếm hoặc chọn khách hàng...</option>
                            @foreach($customers as $index => $customer)
                                <option value="{{ $index }}" 
                                        data-name="{{ $customer->name }}"
                                        data-phone="{{ $customer->phone }}"
                                        data-email="{{ $customer->email }}"
                                        data-address="{{ $customer->address }}">
                                    {{ $customer->name }} - {{ $customer->phone }}
                                </option>
                            @endforeach
                        </select>
                        <input type="hidden" name="customer_id" id="hidden_customer_id" value="">
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Gõ tên hoặc số điện thoại để tìm nhanh khách hàng</div>
                    </div>

                    <div class="mb-3">
                        <label for="customer_name" class="form-label fw-bold">
                            Tên khách hàng <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                               id="customer_name" name="customer_name" value="{{ old('customer_name') }}" 
                               placeholder="Nhập tên khách hàng" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_phone" class="form-label fw-bold">
                            <i class="bi bi-telephone"></i> Số điện thoại <span class="text-danger">*</span>
                        </label>
                        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                               id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" 
                               placeholder="0123456789" required>
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_email" class="form-label fw-bold">
                            <i class="bi bi-envelope"></i> Email
                        </label>
                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                               id="customer_email" name="customer_email" value="{{ old('customer_email') }}" 
                               placeholder="email@example.com">
                        @error('customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="shipping_address" class="form-label fw-bold">
                            <i class="bi bi-geo-alt"></i> Địa chỉ giao hàng <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                  id="shipping_address" name="shipping_address" rows="3" 
                                  placeholder="Nhập địa chỉ giao hàng đầy đủ" required>{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">

                    <div class="mb-3">
                        <label for="payment_method" class="form-label fw-bold">
                            <i class="bi bi-credit-card"></i> Phương thức thanh toán <span class="text-danger">*</span>
                        </label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" 
                                id="payment_method" name="payment_method" required>
                            <option value="">Chọn phương thức thanh toán</option>
                            <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>
                                💵 Thanh toán khi nhận hàng (COD)
                            </option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>
                                🏦 Chuyển khoản ngân hàng
                            </option>
                            <option value="momo" {{ old('payment_method') == 'momo' ? 'selected' : '' }}>
                                💜 Ví điện tử MoMo
                            </option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="receive_at" class="form-label fw-bold">
                            <i class="bi bi-calendar-event"></i> Thời gian nhận hàng
                        </label>
                        <input type="datetime-local" class="form-control @error('receive_at') is-invalid @enderror" 
                               id="receive_at" name="receive_at" 
                               value="{{ old('receive_at', now()->addDays(1)->format('Y-m-d\TH:i')) }}">
                        @error('receive_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Mặc định là ngày mai</div>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label fw-bold">
                            <i class="bi bi-sticky"></i> Ghi chú đơn hàng
                        </label>
                        <textarea class="form-control" id="notes" name="notes" rows="2" 
                                  placeholder="Ghi chú thêm về đơn hàng...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Products & Summary -->
        <div class="col-xl-6 col-lg-6">
            <!-- Products -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-cart-plus"></i> Sản phẩm
                    </h5>
                    <button type="button" class="btn btn-light btn-sm" id="add-product">
                        <i class="bi bi-plus-circle"></i> Thêm sản phẩm
                    </button>
                </div>
                <div class="card-body" style="min-height: 300px;">
                    <div id="products-list">
                        <!-- Products will be added here dynamically -->
                    </div>
                    
                    <div class="text-center text-muted py-5" id="no-products">
                        <i class="bi bi-cart-x" style="font-size: 4rem;"></i>
                        <p class="mt-3">Chưa có sản phẩm nào</p>
                        <small>Nhấn "Thêm sản phẩm" để bắt đầu</small>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0">
                        <i class="bi bi-receipt"></i> Tổng kết đơn hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Tạm tính:</span>
                        <span class="fw-bold" id="subtotal">0 đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Phí vận chuyển:</span>
                        <span class="fw-bold text-success" id="shipping-fee">0 đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="text-muted">Giảm giá:</span>
                        <span class="fw-bold text-danger" id="discount">0 đ</span>
                    </div>
                    <hr class="my-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="fw-bold fs-5">Tổng cộng:</span>
                        <span class="fw-bold text-primary fs-4" id="grand-total">0 đ</span>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-check-circle"></i> Tạo đơn hàng
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row mt-3">
        <div class="col-12">
            <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Quay lại danh sách
            </a>
        </div>
    </div>
</form>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-box-seam"></i> Chọn sản phẩm
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div class="row" id="products-grid">
                    @foreach($products as $product)
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-3">
                            <div class="card product-card h-100 shadow-sm" 
                                 data-product-id="{{ $product->id }}" 
                                 data-product-name="{{ $product->name }}" 
                                 data-product-price="{{ $product->current_price }}"
                                 data-product-stock="{{ $product->stock }}"
                                 style="cursor: pointer; transition: all 0.2s;">
                                <div class="card-body text-center">
                                    @if($product->images->count() > 0)
                                        <img src="{{ product_thumbnail_url($product->images->first()->path) }}" 
                                             class="img-thumbnail mb-3" 
                                             style="width: 100px; height: 100px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center mb-3 mx-auto" 
                                             style="width: 100px; height: 100px; border-radius: 8px;">
                                            <i class="bi bi-image text-muted" style="font-size: 2rem;"></i>
                                        </div>
                                    @endif
                                    <h6 class="mb-2">{{ $product->name }}</h6>
                                    <p class="mb-1 text-muted small">{{ $product->sku }}</p>
                                    <p class="mb-2 fw-bold text-primary">{{ money_vnd($product->current_price) }}</p>
                                    <span class="badge bg-info">{{ $product->stock }} sản phẩm</span>
                                </div>
                                <div class="card-footer bg-transparent text-center border-top-0">
                                    <small class="text-success">
                                        <i class="bi bi-check-circle"></i> Nhấn để chọn
                                    </small>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<style>
.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15) !important;
    border-color: #0d6efd !important;
}

.product-item {
    background: #f8f9fa;
    transition: all 0.2s;
}

.product-item:hover {
    background: #e9ecef;
}

#no-products {
    transition: opacity 0.3s;
}
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productCounter = 0;
    const productsList = document.getElementById('products-list');
    const noProducts = document.getElementById('no-products');
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));
    
    // Initialize Select2 for customer search
    $('#customer_select').select2({
        theme: 'bootstrap-5',
        placeholder: 'Tìm kiếm hoặc chọn khách hàng...',
        allowClear: true,
        width: '100%',
        language: {
            noResults: function() {
                return "Không tìm thấy khách hàng";
            },
            searching: function() {
                return "Đang tìm kiếm...";
            }
        }
    });
    
    // Customer selection - auto-fill info
    $('#customer_select').on('change', function() {
        const selectedValue = $(this).val();
        $('#hidden_customer_id').val(selectedValue || '');
        
        if (selectedValue && selectedValue !== '') {
            const option = $(this).find('option:selected');
            const customerName = option.data('name');
            const customerPhone = option.data('phone');
            const customerEmail = option.data('email') || '';
            const customerAddress = option.data('address') || '';
            
            document.getElementById('customer_name').value = customerName;
            document.getElementById('customer_phone').value = customerPhone;
            document.getElementById('customer_email').value = customerEmail;
            document.getElementById('shipping_address').value = customerAddress;
        } else {
            // Clear fields if deselected
            document.getElementById('customer_name').value = '';
            document.getElementById('customer_phone').value = '';
            document.getElementById('customer_email').value = '';
            document.getElementById('shipping_address').value = '';
        }
    });
    
    // Add product button
    document.getElementById('add-product').addEventListener('click', function() {
        productModal.show();
    });
    
    // Product selection
    document.querySelectorAll('.product-card').forEach(card => {
        card.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = parseFloat(this.dataset.productPrice);
            const productStock = parseInt(this.dataset.productStock);
            
            if (productStock <= 0) {
                alert('Sản phẩm đã hết hàng!');
                return;
            }
            
            addProductToOrder(productId, productName, productPrice);
            productModal.hide();
        });
    });
    
    function addProductToOrder(productId, productName, productPrice) {
        productCounter++;
        
        const productDiv = document.createElement('div');
        productDiv.className = 'product-item border rounded p-3 mb-3';
        productDiv.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-5">
                    <h6 class="mb-1 text-primary">${productName}</h6>
                    <small class="text-muted">Giá: ${money_vnd(productPrice)}</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Số lượng</label>
                    <input type="number" class="form-control quantity-input" 
                           name="items[${productCounter}][quantity]" value="1" min="1" max="999" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-bold">Giá (VNĐ)</label>
                    <input type="number" class="form-control price-input" 
                           name="items[${productCounter}][price]" value="${productPrice}" min="0" step="1000" required>
                </div>
                <div class="col-md-1 text-center">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-product" title="Xóa">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
            <input type="hidden" name="items[${productCounter}][product_id]" value="${productId}">
        `;
        
        productsList.appendChild(productDiv);
        noProducts.style.display = 'none';
        
        // Add event listeners
        productDiv.querySelector('.quantity-input').addEventListener('input', updateTotals);
        productDiv.querySelector('.price-input').addEventListener('input', updateTotals);
        productDiv.querySelector('.remove-product').addEventListener('click', function() {
            productDiv.remove();
            updateTotals();
            if (productsList.children.length === 0) {
                noProducts.style.display = 'block';
            }
        });
        
        updateTotals();
    }
    
    function updateTotals() {
        let subtotal = 0;
        
        document.querySelectorAll('.product-item').forEach(item => {
            const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
            const price = parseFloat(item.querySelector('.price-input').value) || 0;
            subtotal += quantity * price;
        });
        
        const shippingFee = 0;
        const discount = 0;
        const grandTotal = subtotal + shippingFee - discount;
        
        document.getElementById('subtotal').textContent = money_vnd(subtotal);
        document.getElementById('shipping-fee').textContent = money_vnd(shippingFee);
        document.getElementById('discount').textContent = money_vnd(discount);
        document.getElementById('grand-total').textContent = money_vnd(grandTotal);
    }
    
    function money_vnd(amount) {
        return new Intl.NumberFormat('vi-VN').format(Math.round(amount)) + ' đ';
    }
    
    // Form validation
    document.getElementById('create-order-form').addEventListener('submit', function(e) {
        if (productsList.children.length === 0) {
            e.preventDefault();
            alert('⚠️ Vui lòng thêm ít nhất 1 sản phẩm vào đơn hàng!');
            return false;
        }
    });
});
</script>
@endsection
