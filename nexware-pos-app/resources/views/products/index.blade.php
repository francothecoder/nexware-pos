<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products | Nexware POS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="app-shell">
    <aside class="sidebar">
        <div class="brand-block">
            <div class="brand-mark">N</div>
            <div>
                <strong>Nexware POS</strong>
                <span>Catalog</span>
            </div>
        </div>

        <nav class="nav-links">
            <a href="{{ route('dashboard') }}">Dashboard</a>
            <a class="active" href="{{ route('products.index') }}">Products</a>
            <a href="{{ route('pos.index') }}">Point of Sale</a>
        </nav>
    </aside>

    <main class="main-content">
        <header class="topbar">
            <div>
                <p class="eyebrow">Inventory</p>
                <h1>Products catalog</h1>
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

        <section class="panel product-form-panel">
            <h2>Add product</h2>
            <form method="POST" action="{{ route('products.store') }}" class="product-form">
                @csrf

                <div class="field-grid">
                    <label>
                        <span>Name</span>
                        <input type="text" name="name" value="{{ old('name') }}" required>
                    </label>
                    <label>
                        <span>SKU</span>
                        <input type="text" name="sku" value="{{ old('sku') }}" required>
                    </label>
                    <label>
                        <span>Barcode</span>
                        <input type="text" name="barcode" value="{{ old('barcode') }}">
                    </label>
                    <label>
                        <span>Price</span>
                        <input type="number" name="price" step="0.01" min="0" value="{{ old('price', 0) }}" required>
                    </label>
                    <label>
                        <span>Cost</span>
                        <input type="number" name="cost" step="0.01" min="0" value="{{ old('cost', 0) }}" required>
                    </label>
                    <label>
                        <span>Stock</span>
                        <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', 0) }}" required>
                    </label>
                    <label>
                        <span>Low stock alert</span>
                        <input type="number" name="low_stock_level" min="0" value="{{ old('low_stock_level', 5) }}" required>
                    </label>
                    <label class="checkbox-field">
                        <input type="checkbox" name="is_active" value="1" checked>
                        <span>Active product</span>
                    </label>
                </div>

                <label>
                    <span>Description</span>
                    <textarea name="description" rows="3">{{ old('description') }}</textarea>
                </label>

                <button type="submit" class="primary-action">Save product</button>
            </form>
        </section>

        <section class="panel">
            <div class="panel-header">
                <h2>Product list</h2>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>SKU</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->sku }}</td>
                                <td>{{ number_format($product->price, 2) }}</td>
                                <td>{{ $product->stock_quantity }}</td>
                                <td>
                                    <span class="stock-pill {{ $product->stock_quantity <= $product->low_stock_level ? 'critical' : 'healthy' }}">
                                        {{ $product->stock_quantity <= $product->low_stock_level ? 'Low stock' : 'Healthy' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-text">No products added yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrap">
                {{ $products->links() }}
            </div>
        </section>
    </main>
</body>
</html>
