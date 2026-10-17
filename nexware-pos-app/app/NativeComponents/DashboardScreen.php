<?php

namespace App\NativeComponents;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class DashboardScreen extends NativeComponent
{
    public function render(): View
    {
        $todaySales = Sale::query()->whereDate('created_at', today())->sum('total');
        $todayItems = SaleItem::query()
            ->whereHas('sale', fn ($query) => $query->whereDate('created_at', today()))
            ->sum('quantity');
        $lowStockProducts = Product::query()
            ->whereColumn('stock_quantity', '<=', 'low_stock_level')
            ->orderBy('stock_quantity')
            ->limit(5)
            ->get();
        $products = Product::query()->orderBy('stock_quantity')->limit(5)->get();

        return view('native.dashboard', compact('todaySales', 'todayItems', 'lowStockProducts', 'products'));
    }
}
