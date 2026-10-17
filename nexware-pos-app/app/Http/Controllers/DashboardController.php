<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $todaySales = Sale::whereDate('created_at', today())->sum('total');
        $todayItems = SaleItem::whereHas('sale', fn ($query) => $query->whereDate('created_at', today()))->sum('quantity');
        $lowStockProducts = Product::whereColumn('stock_quantity', '<=', 'low_stock_level')
            ->orderBy('stock_quantity', 'asc')
            ->limit(5)
            ->get();

        $products = Product::orderBy('stock_quantity', 'asc')->limit(5)->get();

        return view('dashboard', compact('todaySales', 'todayItems', 'lowStockProducts', 'products'));
    }
}
