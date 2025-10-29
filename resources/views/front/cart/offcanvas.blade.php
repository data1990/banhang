<div class="cart-items-container">
    @if($cart->items->count() > 0)
        <div class="cart-items-list mb-3" style="max-height: 400px; overflow-y: auto;">
            @foreach($cart->items as $item)
                <div class="card mb-2">
                    <div class="card-body p-2">
                        <div class="d-flex align-items-center">
                            @if($item->product->images->count() > 0)
                                <img src="{{ product_thumbnail_url($item->product->images->first()->path) }}" 
                                     class="me-2" 
                                     alt="{{ $item->product->name }}"
                                     style="width: 50px; height: 50px; object-fit: cover; border-radius: 4px;">
                            @else
                                <div class="me-2 bg-light d-flex align-items-center justify-content-center" 
                                     style="width: 50px; height: 50px; border-radius: 4px;">
                                    <i class="bi bi-image text-muted"></i>
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <h6 class="mb-0 small">{{ $item->product->name }}</h6>
                                <small class="text-muted">{{ money_vnd($item->product->current_price) }} x {{ $item->qty }}</small>
                            </div>
                            <button class="btn btn-sm btn-outline-danger remove-item-from-offcanvas" 
                                    data-item-id="{{ $item->id }}">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="border-top pt-3">
            <div class="d-flex justify-content-between mb-2">
                <strong>Tổng cộng:</strong>
                <strong class="text-primary">{{ money_vnd($cart->total) }}</strong>
            </div>
            <div class="d-grid gap-2">
                <a href="{{ route('cart.index') }}" class="btn btn-sm btn-outline-primary">
                    Xem giỏ hàng
                </a>
                <a href="{{ route('checkout.show') }}" class="btn btn-sm btn-primary">
                    Thanh toán
                </a>
            </div>
        </div>
    @else
        <div class="text-center py-4">
            <i class="bi bi-cart-x text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">Giỏ hàng trống</p>
        </div>
    @endif
</div>

<script>
// Remove item from offcanvas
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-item-from-offcanvas')) {
        const button = e.target.closest('.remove-item-from-offcanvas');
        const itemId = button.dataset.itemId;
        
        if (confirm('Xóa sản phẩm này?')) {
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
                    loadCartContent();
                    updateCartCount();
                }
            });
        }
    }
});
</script>


