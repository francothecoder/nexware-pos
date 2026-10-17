<native:column class="fill safe-area bg-stone-50 gap-4 dark:bg-slate-950">
    <native:scroll-view class="fill">
        <native:column class="p-5 gap-5">
            <native:column class="gap-1">
                <native:text class="text-xs font-bold uppercase tracking-widest text-emerald-700 dark:text-lime-300">OPERATIONS</native:text>
                <native:text class="text-3xl font-bold text-slate-950 dark:text-white">Business dashboard</native:text>
            </native:column>

            <native:column class="gap-3">
                <native:row class="gap-3">
                    <native:column class="fill rounded-xl bg-emerald-950 p-4 gap-2">
                        <native:text class="text-sm text-emerald-100">Sales today</native:text>
                        <native:text class="text-2xl font-bold text-white">ZMW {{ number_format($todaySales, 2) }}</native:text>
                    </native:column>
                    <native:column class="fill rounded-xl bg-slate-200 p-4 gap-2 dark:bg-slate-800">
                        <native:text class="text-sm text-slate-600 dark:text-slate-300">Units sold</native:text>
                        <native:text class="text-2xl font-bold text-slate-950 dark:text-white">{{ $todayItems }}</native:text>
                    </native:column>
                </native:row>
                <native:column class="rounded-xl bg-amber-100 p-4 gap-1 dark:bg-amber-950">
                    <native:text class="text-sm text-amber-900 dark:text-amber-100">Low stock items</native:text>
                    <native:text class="text-2xl font-bold text-amber-950 dark:text-white">{{ $lowStockProducts->count() }}</native:text>
                </native:column>
            </native:column>

            <native:column class="gap-3">
                <native:text class="text-lg font-bold text-slate-950 dark:text-white">Stock overview</native:text>
                @forelse ($products as $product)
                    <native:row class="w-full items-center justify-between border-b border-slate-200 py-3 dark:border-slate-700">
                        <native:column class="gap-1">
                            <native:text class="font-semibold text-slate-950 dark:text-white">{{ $product->name }}</native:text>
                            <native:text class="text-sm text-slate-500">{{ $product->sku }}</native:text>
                        </native:column>
                        <native:text class="text-sm text-slate-700 dark:text-slate-200">{{ $product->stock_quantity }} in stock</native:text>
                    </native:row>
                @empty
                    <native:text class="text-sm text-slate-500">No products yet.</native:text>
                @endforelse
            </native:column>
        </native:column>
    </native:scroll-view>

    <native:bottom-nav>
        <native:bottom-nav-item id="dashboard" icon="house" label="Dashboard" url="/app/dashboard" />
        <native:bottom-nav-item id="products" icon="shippingbox" label="Products" url="/app/products" />
        <native:bottom-nav-item id="pos" icon="cart" label="Sell" url="/app/pos" />
    </native:bottom-nav>
</native:column>