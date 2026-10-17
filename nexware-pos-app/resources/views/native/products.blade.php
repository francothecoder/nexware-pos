<native:column class="fill safe-area bg-stone-50 gap-4 dark:bg-slate-950">
    <native:scroll-view class="fill">
        <native:column class="p-5 gap-5">
            <native:column class="gap-1">
                <native:text class="text-xs font-bold uppercase tracking-widest text-emerald-700 dark:text-lime-300">INVENTORY</native:text>
                <native:text class="text-3xl font-bold text-slate-950 dark:text-white">Products</native:text>
            </native:column>

            @if ($message !== '')
                <native:text class="text-sm font-semibold text-emerald-800 dark:text-lime-300">{{ $message }}</native:text>
            @endif

            <native:column class="gap-3">
                <native:text class="text-lg font-bold text-slate-950 dark:text-white">Add product</native:text>
                <native:outlined-text-input label="Product name" native:model="name" />
                <native:outlined-text-input label="SKU" native:model="sku" />
                <native:row class="gap-3">
                    <native:outlined-text-input class="fill" label="Price (ZMW)" native:model="price" keyboard-type="decimal" />
                    <native:outlined-text-input class="fill" label="Cost (ZMW)" native:model="cost" keyboard-type="decimal" />
                </native:row>
                <native:row class="gap-3">
                    <native:outlined-text-input class="fill" label="Stock" native:model="stockQuantity" keyboard-type="number" />
                    <native:outlined-text-input class="fill" label="Low stock alert" native:model="lowStockLevel" keyboard-type="number" />
                </native:row>
                <native:button label="Save product" @press="saveProduct" />
            </native:column>

            <native:column class="gap-3">
                <native:text class="text-lg font-bold text-slate-950 dark:text-white">Product list</native:text>
                @forelse ($products as $product)
                    <native:row class="w-full items-center justify-between border-b border-slate-200 py-3 dark:border-slate-700">
                        <native:column class="gap-1">
                            <native:text class="font-semibold text-slate-950 dark:text-white">{{ $product->name }}</native:text>
                            <native:text class="text-sm text-slate-500">{{ $product->sku }} · ZMW {{ number_format($product->price, 2) }}</native:text>
                        </native:column>
                        <native:text class="text-sm text-slate-700 dark:text-slate-200">{{ $product->stock_quantity }} in stock</native:text>
                    </native:row>
                @empty
                    <native:text class="text-sm text-slate-500">No products added yet.</native:text>
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