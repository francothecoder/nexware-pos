<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\SaleCheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PosController extends Controller
{
    public function index(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('pos.index', compact('products'));
    }

    public function checkout(Request $request, SaleCheckoutService $checkout): RedirectResponse
    {
        $validated = $request->validate([
            'customer_name' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $sale = $checkout->complete($validated['customer_name'] ?? null, $validated['items']);

        return redirect()->route('pos.index')->with('success', 'Sale recorded for transaction #'.$sale->transaction_number.'.');
    }
}
