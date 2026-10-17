<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>POS | Nexware POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell">
    <aside class="sidebar">
        <div class="brand-block">
            <div class="brand-mark">N</div>
            <div>
                <strong>Nexware POS</strong>
                <span>Checkout</span>
            </div>
        </div>

        <nav class="nav-links">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('products.index') }}">Products</a>
            <a class="active" href="{{ route('pos.index') }}">Point of Sale</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <p class="eyebrow">Checkout</p>
                <h1>Point of sale</h1>
            </div>
        </header>

        @if (session('success'))
            <div class="flash success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="flash error">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <section class="pos-layout">
            <div class="panel product-panel">
                <h2>Products</h2>

                <div class="product-grid">
                    @forelse ($products as $product)
                        <div class="pos-product-card">
                            <div class="product-header">
                                <h3>{{ $product->name }}</h3>
                                <span>{{ number_format($product->price, 2) }}</span>
                            </div>
                            <p>{{ $product->stock_quantity }} in stock</p>
                            <label>
                                <span>Qty</span>
                                <input type="number" form="sale-form" name="items[{{ $product->id }}][quantity]" min="0" max="{{ $product->stock_quantity }}" value="0">
                                <input type="hidden" form="sale-form" name="items[{{ $product->id }}][product_id]" value="{{ $product->id }}">
                            </label>
                        </div>
                    @empty
                        <p class="empty-text">No active products available.</p>
                    @endforelse
                </div>
            </div>

            <div class="panel checkout-panel">
                <h2>Checkout</h2>
                <form id="sale-form" method="POST" action="{{ route('pos.checkout') }}">
                    @csrf

                    <label>
                        <span>Customer</span>
                        <input type="text" name="customer_name" value="{{ old('customer_name', 'Walk-in customer') }}">
                    </label>

                    <div class="totals-box">
                        <div><span>Payment mode</span><strong>Cash</strong></div>
                        <div><span>Order</span><strong>Ready to sell</strong></div>
                    </div>

                    <button type="submit" class="primary-action">Complete sale</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
