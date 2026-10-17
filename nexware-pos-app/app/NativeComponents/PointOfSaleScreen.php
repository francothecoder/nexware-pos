<?php

namespace App\NativeComponents;

use App\Models\Product;
use App\Services\SaleCheckoutService;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class PointOfSaleScreen extends NativeComponent
{
    public array $cart = [];

    public string $customerName = '';

    public string $message = '';

    public function render(): View
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $total = $products->sum(fn (Product $product) => (float) $product->price * ($this->cart[$product->id] ?? 0));

        return view('native.pos', compact('products', 'total'));
    }

    public function addProduct(int $productId): void
    {
        $product = Product::query()->whereKey($productId)->where('is_active', true)->first();

        if (! $product || ($this->cart[$productId] ?? 0) >= $product->stock_quantity) {
            $this->message = 'No more stock is available for that product.';

            return;
        }

        $this->cart[$productId] = ($this->cart[$productId] ?? 0) + 1;
        $this->message = '';
    }

    public function removeProduct(int $productId): void
    {
        if (($this->cart[$productId] ?? 0) <= 1) {
            unset($this->cart[$productId]);

            return;
        }

        $this->cart[$productId]--;
    }

    public function completeSale(SaleCheckoutService $checkout): void
    {
        try {
            $sale = $checkout->complete($this->customerName, collect($this->cart)
                ->map(fn (int $quantity, int $productId) => [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                ])
                ->values()
                ->all());
        } catch (ValidationException $exception) {
            $this->message = $exception->validator->errors()->first();

            return;
        }

        $this->cart = [];
        $this->customerName = '';
        $this->message = 'Sale recorded: '.$sale->transaction_number;
    }
}
