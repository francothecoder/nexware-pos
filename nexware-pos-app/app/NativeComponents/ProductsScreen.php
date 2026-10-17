<?php

namespace App\NativeComponents;

use App\Models\Product;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Native\Mobile\Edge\NativeComponent;

class ProductsScreen extends NativeComponent
{
    public string $name = '';

    public string $sku = '';

    public string $price = '';

    public string $cost = '';

    public string $stockQuantity = '';

    public string $lowStockLevel = '5';

    public string $message = '';

    public function render(): View
    {
        $products = Product::query()->orderBy('name')->get();

        return view('native.products', compact('products'));
    }

    public function saveProduct(): void
    {
        $validator = Validator::make([
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'cost' => $this->cost,
            'stock_quantity' => $this->stockQuantity,
            'low_stock_level' => $this->lowStockLevel,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:100', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost' => ['required', 'numeric', 'min:0'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'low_stock_level' => ['required', 'integer', 'min:0'],
        ]);

        if ($validator->fails()) {
            $this->message = $validator->errors()->first();

            return;
        }

        Product::query()->create($validator->validated() + [
            'is_active' => true,
        ]);

        $this->name = '';
        $this->sku = '';
        $this->price = '';
        $this->cost = '';
        $this->stockQuantity = '';
        $this->lowStockLevel = '5';
        $this->message = 'Product saved.';
    }
}
