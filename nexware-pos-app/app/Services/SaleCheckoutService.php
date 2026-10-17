<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SaleCheckoutService
{
    public function complete(?string $customerName, array $items): Sale
    {
        $items = collect($items)->filter(fn (array $item) => (int) $item['quantity'] > 0);

        if ($items->isEmpty()) {
            throw ValidationException::withMessages(['items' => 'Add at least one product to the sale.']);
        }

        $products = Product::query()->whereIn('id', $items->pluck('product_id'))->get()->keyBy('id');

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);

            if (! $product || $product->stock_quantity < $item['quantity']) {
                throw ValidationException::withMessages([
                    'items' => 'Not enough stock for '.($product?->name ?? 'the selected product').'.',
                ]);
            }
        }

        $subtotal = 0;
        $saleItems = [];

        foreach ($items as $item) {
            $product = $products->get($item['product_id']);
            $lineTotal = (float) $product->price * (int) $item['quantity'];
            $subtotal += $lineTotal;

            $saleItems[] = [
                'product_id' => $product->id,
                'quantity' => (int) $item['quantity'],
                'unit_price' => $product->price,
                'total_price' => $lineTotal,
            ];
        }

        return DB::transaction(function () use ($customerName, $saleItems, $subtotal, $products) {
            $sale = Sale::query()->create([
                'transaction_number' => 'POS-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4)),
                'customer_name' => $customerName ?: 'Walk-in customer',
                'subtotal' => $subtotal,
                'tax_total' => 0,
                'total' => $subtotal,
                'paid_amount' => $subtotal,
                'change_due' => 0,
                'status' => 'completed',
            ]);

            foreach ($saleItems as $saleItem) {
                $sale->items()->create($saleItem);

                $products->get($saleItem['product_id'])->decrement('stock_quantity', $saleItem['quantity']);
            }

            return $sale;
        });
    }
}
