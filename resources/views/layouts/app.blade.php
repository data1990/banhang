<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \App\Models\Setting::get('store.name', config('app.name', 'BanHang')) }} - @yield('title', 'Trang chủ')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Custom CSS -->
    <style>
        .navbar-brand {
            font-weight: 600;
        }
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .product-card {
            transition: transform 0.2s;
        }
        .product-card:hover {
            transform: translateY(-2px);
        }
        .price-original {
            text-decoration: line-through;
            color: #6c757d;
        }
        .price-sale {
            color: #dc3545;
            font-weight: 600;
        }
        .badge-sale {
            position: absolute;
            top: 10px;
            right: 10px;
            background: #dc3545;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
        }
        .footer {
            background-color: #f8f9fa;
            margin-top: 50px;
        }
        
        /* Mobile menu improvements */
        @media (max-width: 991.98px) {
            .navbar-collapse {
                background: white;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                margin-top: 10px;
                padding: 15px;
            }
            .navbar-nav .nav-link {
                padding: 8px 0;
                border-bottom: 1px solid #eee;
            }
            .navbar-nav .nav-link:last-child {
                border-bottom: none;
            }
            .d-flex.me-3 {
                margin: 10px 0 !important;
            }
        }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div id="app">
        <!-- Navigation -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('home') }}">
                    @php($logo = \App\Models\Setting::get('store.logo_url'))
                    @if($logo)
                        <img src="{{ $logo }}" alt="Logo" style="height:28px">
                    @else
                        <i class="bi bi-shop"></i>
                    @endif
                    <span>{{ \App\Models\Setting::get('store.name', config('app.name', 'BanHang')) }}</span>
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('home') }}">Trang chủ</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('products.index') }}">Sản phẩm</a>
                        </li>
                    </ul>

                    <!-- Search -->
                    <form class="d-flex me-3" method="GET" action="{{ route('products.index') }}">
                        <input class="form-control me-2" type="search" name="search" placeholder="Tìm kiếm sản phẩm..." value="{{ request('search') }}">
                        <button class="btn btn-outline-primary" type="submit">
                            <i class="bi bi-search"></i>
                        </button>
                    </form>

                    <!-- Cart -->
                    <div class="position-relative me-3">
                        <button class="btn btn-outline-primary" id="cart-btn" data-bs-toggle="offcanvas" data-bs-target="#cartOffcanvas">
                            <i class="bi bi-cart3"></i>
                            <span class="cart-badge" id="cart-count">0</span>
                        </button>
                    </div>

                    <!-- Auth Links -->
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> {{ auth()->user()->name }}
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('account.dashboard') }}">Tài khoản</a></li>
                                <li><a class="dropdown-item" href="{{ route('account.orders') }}">Đơn hàng</a></li>
                                @if(auth()->user()->isStaff())
                                    <li><hr class="dropdown-divider"></li>
                                    <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}">Quản trị</a></li>
                                @endif
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item">Đăng xuất</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="btn btn-primary">Đăng ký</a>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer py-4">
            <div class="container">
                <div class="row">
                    <div class="col-md-4">
                        <h5>{{ \App\Models\Setting::get('store.name', config('app.name', 'BanHang')) }}</h5>
                        <p class="text-muted">Hệ thống thương mại điện tử hiện đại với tích hợp Zalo và Messenger.</p>
                    </div>
                    <div class="col-md-4">
                        <h5>Liên kết</h5>
                        <ul class="list-unstyled">
                            <li><a href="{{ route('home') }}" class="text-decoration-none">Trang chủ</a></li>
                            <li><a href="{{ route('products.index') }}" class="text-decoration-none">Sản phẩm</a></li>
                            @auth
                                <li><a href="{{ route('account.dashboard') }}" class="text-decoration-none">Tài khoản</a></li>
                            @endauth
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h5>Liên hệ</h5>
                        <p class="text-muted">
                            <i class="bi bi-telephone"></i> {{ \App\Models\Setting::get('store.contact_phone', '0123456789') }}<br>
                            <i class="bi bi-geo-alt"></i> {{ \App\Models\Setting::get('store.address', '123 Đường ABC, Quận 1, TP.HCM') }}
                        </p>
                        <div>
                            @if(\App\Models\Setting::get('store.messenger_link'))
                                <a href="{{ \App\Models\Setting::get('store.messenger_link') }}" class="btn btn-outline-primary btn-sm me-2" target="_blank">
                                    <i class="bi bi-messenger"></i> Messenger
                                </a>
                            @endif
                            @if(\App\Models\Setting::get('store.zalo_link'))
                                <a href="{{ \App\Models\Setting::get('store.zalo_link') }}" class="btn btn-outline-success btn-sm" target="_blank">
                                    <i class="bi bi-chat-dots"></i> Zalo
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
                <hr>
                <div class="text-center text-muted">
                    <p>&copy; {{ date('Y') }} {{ \App\Models\Setting::get('store.name', config('app.name', 'BanHang')) }}. Tất cả quyền được bảo lưu.</p>
                </div>
            </div>
        </footer>
    </div>

    <!-- Cart Offcanvas -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="cartOffcanvas">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title">Giỏ hàng</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body" id="cart-content">
            <!-- Cart items will be loaded here -->
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- Custom JS -->
    <script>
        // CSRF token for AJAX requests
        window.Laravel = {
            csrfToken: '{{ csrf_token() }}'
        };

        // Update cart count
        function updateCartCount() {
            fetch('/api/cart/count')
                .then(response => response.json())
                .then(data => {
                    document.getElementById('cart-count').textContent = data.count || 0;
                })
                .catch(error => console.error('Error:', error));
        }

        // Load cart content
        function loadCartContent() {
            fetch('{{ route("cart.offcanvas") }}')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('cart-content').innerHTML = html;
                })
                .catch(error => console.error('Error loading cart:', error));
        }

        // Format money
        function formatMoney(amount) {
            return new Intl.NumberFormat('vi-VN').format(amount) + ' đ';
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateCartCount();
            
            // Load cart content when offcanvas is shown
            const cartBtn = document.getElementById('cart-btn');
            const cartOffcanvas = document.getElementById('cartOffcanvas');
            
            if (cartBtn && cartOffcanvas) {
                cartOffcanvas.addEventListener('show.bs.offcanvas', function () {
                    loadCartContent();
                });
            }
        });
    </script>

    @yield('scripts')
</body>
</html>