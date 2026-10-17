<native:column class="fill safe-area bg-stone-50 gap-4 dark:bg-slate-950">
    <native:scroll-view class="fill">
        <native:column class="p-5 gap-5">
            <native:column class="gap-1">
                <native:text class="text-xs font-bold uppercase tracking-widest text-emerald-700 dark:text-lime-300">CHECKOUT</native:text>
                <native:text class="text-3xl font-bold text-slate-950 dark:text-white">Point of sale</native:text>
            </native:column>

            @if ($message !== '')
                <native:text class="text-sm font-semibold text-emerald-800 dark:text-lime-300">{{ $message }}</native:text>
            @endif

            <native:column class="gap-3">
                <native:text class="text-lg font-bold text-slate-950 dark:text-white">Choose products</native:text>
                @forelse ($products as $product)
                    <native:row class="w-full items-center justify-between border-b border-slate-200 py-3 dark:border-slate-700">
                        <native:column class="gap-1">
                            <native:text class="font-semibold text-slate-950 dark:text-white">{{ $product->name }}</native:text>
                            <native:text class="text-sm text-slate-500">ZMW {{ number_format($product->price, 2) }} · {{ $product->stock_quantity }} in stock</native:text>
                        </native:column>
                        <native:row class="items-center gap-2">
                            <native:button label="−" variant="secondary" size="sm" @press="removeProduct({{ $product->id }})" />
                            <native:text class="text-base font-semibold text-slate-950 dark:text-white">{{ $cart[$product->id] ?? 0 }}</native:text>
                            <native:button label="+" size="sm" @press="addProduct({{ $product->id }})" />
                        </native:row>
                    </native:row>
                @empty
                    <native:text class="text-sm text-slate-500">No active products available.</native:text>
                @endforelse
            </native:column>

            <native:column class="gap-3">
                <native:text class="text-lg font-bold text-slate-950 dark:text-white">Checkout</native:text>
                <native:outlined-text-input label="Customer (optional)" native:model="customerName" />
                <native:row class="w-full items-center justify-between py-2">
                    <native:text class="text-base font-semibold text-slate-700 dark:text-slate-200">Total</native:text>
                    <native:text class="text-xl font-bold text-slate-950 dark:text-white">ZMW {{ number_format($total, 2) }}</native:text>
                </native:row>
                <native:button label="Complete sale" @press="completeSale" :disabled="count($cart) === 0" />
            </native:column>
        </native:column>
    </native:scroll-view>

    <native:bottom-nav>
        <native:bottom-nav-item id="dashboard" icon="house" label="Dashboard" url="/app/dashboard" />
        <native:bottom-nav-item id="products" icon="shippingbox" label="Products" url="/app/products" />
        <native:bottom-nav-item id="pos" icon="cart" label="Sell" url="/app/pos" />
    </native:bottom-nav>
</native:column>