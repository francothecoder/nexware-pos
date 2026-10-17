<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosCoreFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_renders_business_overview(): void
    {
        Product::create([
            'name' => 'Rice Bag',
            'sku' => 'RICE-001',
            'price' => 120.00,
            'cost' => 80.00,
            'stock_quantity' => 12,
            'low_stock_level' => 5,
            'is_active' => true,
        ]);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Business dashboard')
            ->assertSee('Sales today')
            ->assertSee('Low stock');
    }

    public function test_product_can_be_created(): void
    {
        $this->post('/products', [
            'name' => 'Soap Bar',
            'sku' => 'SOAP-100',
            'price' => 15.50,
            'cost' => 8.00,
            'stock_quantity' => 24,
            'low_stock_level' => 6,
            'is_active' => true,
        ])->assertRedirect('/products');

        $this->assertDatabaseHas('products', [
            'name' => 'Soap Bar',
            'sku' => 'SOAP-100',
            'stock_quantity' => 24,
        ]);
    }

    public function test_sale_checkout_reduces_stock_and_records_transaction(): void
    {
        $product = Product::create([
            'name' => 'Tomato Sauce',
            'sku' => 'TOM-505',
            'price' => 25.00,
            'cost' => 14.00,
            'stock_quantity' => 7,
            'low_stock_level' => 2,
            'is_active' => true,
        ]);

        $this->post('/pos/checkout', [
            'customer_name' => 'Jane Doe',
            'items' => [
                ['product_id' => $product->id, 'quantity' => 2],
            ],
        ])->assertRedirect('/pos');

        $this->assertDatabaseHas('sales', [
            'customer_name' => 'Jane Doe',
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('sale_items', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $product->refresh();

        $this->assertSame(5, $product->stock_quantity);
    }
}
