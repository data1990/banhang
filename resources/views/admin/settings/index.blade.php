@extends('layouts.admin')

@section('title', 'Cài đặt hệ thống')
@section('page-title', 'Cài đặt hệ thống')

@section('content')
<form method="POST" action="{{ route('admin.settings.update') }}">
    @csrf
    
    <div class="row">
        <!-- Bank Transfer Settings -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-credit-card"></i> Thông tin chuyển khoản
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="bank_transfer_info" class="form-label">Thông tin chuyển khoản</label>
                        <textarea class="form-control @error('bank_transfer_info') is-invalid @enderror" 
                                  id="bank_transfer_info" name="bank_transfer_info" rows="6" 
                                  placeholder="Nhập thông tin ngân hàng, số tài khoản...">{{ old('bank_transfer_info', $settings['bank']['transfer_info']) }}</textarea>
                        <div class="form-text">Hỗ trợ HTML. Ví dụ: &lt;h4&gt;Thông tin chuyển khoản&lt;/h4&gt;</div>
                        @error('bank_transfer_info')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Store Information -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="bi bi-shop"></i> Thông tin cửa hàng
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="store_contact_phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control @error('store_contact_phone') is-invalid @enderror" 
                               id="store_contact_phone" name="store_contact_phone" 
                               value="{{ old('store_contact_phone', $settings['store']['contact_phone']) }}">
                        @error('store_contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="store_address" class="form-label">Địa chỉ</label>
                        <textarea class="form-control @error('store_address') is-invalid @enderror" 
                                  id="store_address" name="store_address" rows="3">{{ old('store_address', $settings['store']['address']) }}</textarea>
                        @error('store_address')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="store_messenger_link" class="form-label">Link Messenger</label>
                        <input type="url" class="form-control @error('store_messenger_link') is-invalid @enderror" 
                               id="store_messenger_link" name="store_messenger_link" 
                               value="{{ old('store_messenger_link', $settings['store']['messenger_link']) }}"
                               placeholder="https://m.me/yourpage">
                        @error('store_messenger_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="store_zalo_link" class="form-label">Link Zalo</label>
                        <input type="url" class="form-control @error('store_zalo_link') is-invalid @enderror" 
                               id="store_zalo_link" name="store_zalo_link" 
                               value="{{ old('store_zalo_link', $settings['store']['zalo_link']) }}"
                               placeholder="https://zalo.me/yourzalo">
                        @error('store_zalo_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Zalo Integration -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-chat-dots"></i> Tích hợp Zalo
                    </h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="zalo_enabled" name="zalo_enabled" 
                               value="1" {{ old('zalo_enabled', $settings['zalo']['enabled']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="zalo_enabled">Bật thông báo Zalo</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="zalo_oa_id" class="form-label">Zalo OA ID</label>
                        <input type="text" class="form-control @error('zalo_oa_id') is-invalid @enderror" 
                               id="zalo_oa_id" name="zalo_oa_id" 
                               value="{{ old('zalo_oa_id', $settings['zalo']['oa_id']) }}">
                        @error('zalo_oa_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="zalo_access_token" class="form-label">Access Token</label>
                        <input type="password" class="form-control @error('zalo_access_token') is-invalid @enderror" 
                               id="zalo_access_token" name="zalo_access_token" 
                               value="{{ old('zalo_access_token', $settings['zalo']['access_token']) }}"
                               placeholder="Nhập token để cập nhật">
                        @error('zalo_access_token')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Để trống nếu không muốn thay đổi token hiện tại</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" formaction="{{ route('admin.settings.test-zalo') }}" 
                                class="btn btn-outline-success">
                            <i class="bi bi-check-circle"></i> Test kết nối Zalo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messenger Integration -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-messenger"></i> Tích hợp Messenger
                    </h5>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="messenger_enabled" name="messenger_enabled" 
                               value="1" {{ old('messenger_enabled', $settings['messenger']['enabled']) ? 'checked' : '' }}>
                        <label class="form-check-label" for="messenger_enabled">Bật thông báo Messenger</label>
                    </div>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="messenger_page_id" class="form-label">Page ID</label>
                        <input type="text" class="form-control @error('messenger_page_id') is-invalid @enderror" 
                               id="messenger_page_id" name="messenger_page_id" 
                               value="{{ old('messenger_page_id', $settings['messenger']['page_id']) }}">
                        @error('messenger_page_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="messenger_page_token" class="form-label">Page Token</label>
                        <input type="password" class="form-control @error('messenger_page_token') is-invalid @enderror" 
                               id="messenger_page_token" name="messenger_page_token" 
                               value="{{ old('messenger_page_token', $settings['messenger']['page_token']) }}"
                               placeholder="Nhập token để cập nhật">
                        @error('messenger_page_token')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div class="form-text">Để trống nếu không muốn thay đổi token hiện tại</div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" formaction="{{ route('admin.settings.test-messenger') }}" 
                                class="btn btn-outline-primary">
                            <i class="bi bi-check-circle"></i> Test kết nối Messenger
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Button -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">Lưu cài đặt</h6>
                            <small class="text-muted">Tất cả thay đổi sẽ được lưu khi bạn nhấn nút "Lưu cài đặt"</small>
                        </div>
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-save"></i> Lưu cài đặt
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle integration sections based on checkboxes
    function toggleIntegrationSection(checkboxId, sectionClass) {
        const checkbox = document.getElementById(checkboxId);
        const sections = document.querySelectorAll(sectionClass);
        
        function updateVisibility() {
            sections.forEach(section => {
                if (checkbox.checked) {
                    section.style.opacity = '1';
                    section.disabled = false;
                } else {
                    section.style.opacity = '0.5';
                    section.disabled = true;
                }
            });
        }
        
        checkbox.addEventListener('change', updateVisibility);
        updateVisibility(); // Initial state
    }
    
    // Apply to both integrations
    toggleIntegrationSection('zalo_enabled', '.zalo-section');
    toggleIntegrationSection('messenger_enabled', '.messenger-section');
    
    // Test connection buttons
    document.querySelectorAll('button[formaction*="test-"]').forEach(button => {
        button.addEventListener('click', function(e) {
            const form = this.closest('form');
            const formData = new FormData(form);
            
            // Add test flag
            formData.append('test_connection', '1');
            
            fetch(this.formAction, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': window.Laravel.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Kết nối thành công!');
                } else {
                    alert('Kết nối thất bại: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Có lỗi xảy ra khi test kết nối');
            });
            
            e.preventDefault();
        });
    });
});
</script>
@endsection
