<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nexware POS Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell">
    <aside class="sidebar">
        <div class="brand-block">
            <div class="brand-mark">N</div>
            <div>
                <strong>Nexware POS</strong>
                <span>Operations</span>
            </div>
        </div>

        <nav class="nav-links">
            <a class="active" href="{{ route('dashboard') }}">Dashboard</a>
            <a href="{{ route('products.index') }}">Products</a>
            <a href="{{ route('pos.index') }}">Point of Sale</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <p class="eyebrow">Overview</p>
                <h1>Business dashboard</h1>
            </div>
            <a href="{{ route('pos.index') }}" class="primary-action">New sale</a>
        </header>

        <section class="stats-grid">
            <article class="stat-card accent-green">
                <span>Sales today</span>
                <strong>ZMW {{ number_format($todaySales ?? 0, 2) }}</strong>
            </article>
            <article class="stat-card accent-slate">
                <span>Units sold</span>
                <strong>{{ $todayItems ?? 0 }}</strong>
            </article>
            <article class="stat-card accent-amber">
                <span>Low stock</span>
                <strong>{{ $lowStockProducts->count() }}</strong>
            </article>
        </section>

        <section class="panel-grid">
            <article class="panel">
                <div class="panel-header">
                    <h2>Stock overview</h2>
                    <a href="{{ route('products.index') }}">Manage</a>
                </div>

                <div class="list-stack">
                    @forelse ($products as $product)
                        <div class="list-row">
                            <div>
                                <strong>{{ $product->name }}</strong>
                                <small>{{ $product->sku }}</small>
                            </div>
                            <span class="stock-pill {{ $product->stock_quantity <= $product->low_stock_level ? 'critical' : '' }}">
                                {{ $product->stock_quantity }} in stock
                            </span>
                        </div>
                    @empty
                        <p class="empty-text">No products yet. Add your first item.</p>
                    @endforelse
                </div>
            </article>

            <article class="panel">
                <div class="panel-header">
                    <h2>Low stock alerts</h2>
                </div>

                <div class="list-stack">
                    @forelse ($lowStockProducts as $product)
                        <div class="list-row">
                            <div>
                                <strong>{{ $product->name }}</strong>
                                <small>Reorder threshold: {{ $product->low_stock_level }}</small>
                            </div>
                            <span class="stock-pill critical">{{ $product->stock_quantity }} left</span>
                        </div>
                    @empty
                        <p class="empty-text">Everything looks healthy.</p>
                    @endforelse
                </div>
            </article>
        </section>
    </main>
</body>
</html>
