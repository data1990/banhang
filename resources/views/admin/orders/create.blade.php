@extends('layouts.admin')

@section('title', 'Tạo đơn hàng mới')
@section('page-title', 'Tạo đơn hàng mới')

@section('content')
<form action="{{ route('admin.orders.store') }}" method="POST" id="create-order-form">
    @csrf
    
    <div class="row">
        <!-- Customer Information -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Thông tin khách hàng</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Khách hàng có tài khoản</label>
                        <select class="form-select @error('customer_id') is-invalid @enderror" 
                                id="customer_id" name="customer_id">
                            <option value="">Chọn khách hàng (tùy chọn)</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" 
                                        {{ old('customer_id') == $customer->id ? 'selected' : '' }}>
                                    {{ $customer->name }} - {{ $customer->email }}
                                </option>
                            @endforeach
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Tên khách hàng *</label>
                        <input type="text" class="form-control @error('customer_name') is-invalid @enderror" 
                               id="customer_name" name="customer_name" value="{{ old('customer_name') }}" required>
                        @error('customer_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_phone" class="form-label">Số điện thoại *</label>
                        <input type="text" class="form-control @error('customer_phone') is-invalid @enderror" 
                               id="customer_phone" name="customer_phone" value="{{ old('customer_phone') }}" required>
                        @error('customer_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="customer_email" class="form-label">Email</label>
                        <input type="email" class="form-control @error('customer_email') is-invalid @enderror" 
                               id="customer_email" name="customer_email" value="{{ old('customer_email') }}">
                        @error('customer_email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="shipping_address" class="form-label">Địa chỉ giao hàng *</label>
                        <textarea class="form-control @error('shipping_address') is-invalid @enderror" 
                                  id="shipping_address" name="shipping_address" rows="3" required>{{ old('shipping_address') }}</textarea>
                        @error('shipping_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Phương thức thanh toán *</label>
                        <select class="form-select @error('payment_method') is-invalid @enderror" 
                                id="payment_method" name="payment_method" required>
                            <option value="">Chọn phương thức thanh toán</option>
                            <option value="cod" {{ old('payment_method') == 'cod' ? 'selected' : '' }}>Thanh toán khi nhận hàng (COD)</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Chuyển khoản ngân hàng</option>
                            <option value="momo" {{ old('payment_method') == 'momo' ? 'selected' : '' }}>Ví điện tử MoMo</option>
                        </select>
                        @error('payment_method')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="receive_at" class="form-label">Thời gian nhận hàng</label>
                        <input type="datetime-local" class="form-control @error('receive_at') is-invalid @enderror" 
                               id="receive_at" name="receive_at" 
                               value="{{ old('receive_at', now()->addDays(1)->format('Y-m-d\TH:i')) }}">
                        @error('receive_at')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Mặc định là ngày mai</div>
                    </div>

                    <div class="mb-3">
                </div>
            </div>
        </div>

        <!-- Products -->
        <div class="col-lg-6">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sản phẩm</h5>
                    <button type="button" class="btn btn-sm btn-primary" id="add-product">
                        <i class="bi bi-plus"></i> Thêm sản phẩm
                    </button>
                </div>
                <div class="card-body">
                    <div id="products-list">
                        <!-- Products will be added here dynamically -->
                    </div>
                    
                    <div class="text-center text-muted" id="no-products">
                        <i class="bi bi-cart-x" style="font-size: 2rem;"></i>
                        <p>Chưa có sản phẩm nào</p>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Tổng kết đơn hàng</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Tạm tính:</span>
                        <span id="subtotal">0 đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Phí vận chuyển:</span>
                        <span id="shipping-fee">0 đ</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Giảm giá:</span>
                        <span id="discount">0 đ</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Tổng cộng:</span>
                        <span id="grand-total">0 đ</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> Tạo đơn hàng
                </button>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Quay lại
                </a>
            </div>
        </div>
    </div>
</form>

<!-- Product Selection Modal -->
<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row" id="products-grid">
                    @foreach($products as $product)
                        <div class="col-md-6 mb-3">
                            <div class="card product-card" data-product-id="{{ $product->id }}" 
                                 data-product-name="{{ $product->name }}" 
                                 data-product-price="{{ $product->current_price }}">
                                <div class="card-body">
                                    <div class="d-flex">
                                        <div class="me-3">
                                            @if($product->images->count() > 0)
                                                <img src="{{ product_thumbnail_url($product->images->first()->path) }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 60px; height: 60px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 60px; height: 60px;">
                                                    <i class="bi bi-image text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">{{ $product->name }}</h6>
                                            <p class="mb-1 text-muted small">{{ $product->sku }}</p>
                                            <p class="mb-0 fw-bold text-primary">{{ money_vnd($product->current_price) }}</p>
                                            <small class="text-muted">Tồn kho: {{ $product->stock }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    let productCounter = 0;
    const productsList = document.getElementById('products-list');
    const noProducts = document.getElementById('no-products');
    const productModal = new bootstrap.Modal(document.getElementById('productModal'));
    
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
            
            addProductToOrder(productId, productName, productPrice);
            productModal.hide();
        });
    });
    
    // Customer selection
    document.getElementById('customer_id').addEventListener('change', function() {
        if (this.value) {
            // Auto-fill customer info if customer is selected
            const option = this.options[this.selectedIndex];
            const customerName = option.text.split(' - ')[0];
            document.getElementById('customer_name').value = customerName;
        }
    });
    
    function addProductToOrder(productId, productName, productPrice) {
        productCounter++;
        
        const productDiv = document.createElement('div');
        productDiv.className = 'product-item border rounded p-3 mb-3';
        productDiv.innerHTML = `
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h6 class="mb-1">${productName}</h6>
                    <small class="text-muted">${money_vnd(productPrice)}</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Số lượng</label>
                    <input type="number" class="form-control form-control-sm quantity-input" 
                           name="items[${productCounter}][quantity]" value="1" min="1" max="999">
                </div>
                <div class="col-md-3">
                    <label class="form-label small">Giá</label>
                    <input type="number" class="form-control form-control-sm price-input" 
                           name="items[${productCounter}][price]" value="${productPrice}" min="0" step="1000">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-sm btn-outline-danger remove-product">
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
        return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
    }
    
    // Form validation
    document.getElementById('create-order-form').addEventListener('submit', function(e) {
        if (productsList.children.length === 0) {
            e.preventDefault();
            alert('Vui lòng thêm ít nhất 1 sản phẩm');
            return false;
        }
    });
});
</script>
@endsection
