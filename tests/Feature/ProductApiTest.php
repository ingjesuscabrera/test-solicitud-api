<?php

use App\Models\Currency;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->usd = Currency::create([
        'name' => 'US Dollar',
        'symbol' => 'USD',
        'exchange_rate' => 1.0000,
    ]);

    $this->eur = Currency::create([
        'name' => 'Euro',
        'symbol' => 'EUR',
        'exchange_rate' => 0.9200,
    ]);
});

test('it can list products with pagination and relations', function () {
    Product::create([
        'name' => 'Keyboard Mechanical',
        'description' => 'RGB switch blue',
        'price' => 89.99,
        'currency_id' => $this->usd->id,
        'tax_cost' => 10.00,
        'manufacturing_cost' => 35.00,
    ]);

    $response = $this->getJson('/api/products');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                    'price',
                    'currency_id',
                    'tax_cost',
                    'manufacturing_cost',
                    'total_cost',
                    'currency' => ['id', 'name', 'symbol'],
                ]
            ],
            'links',
            'meta'
        ]);
});

test('it can create a product with valid data', function () {
    $payload = [
        'name' => 'Wireless Mouse',
        'description' => 'Ergonomic 2.4Ghz',
        'price' => 45.50,
        'currency_id' => $this->usd->id,
        'tax_cost' => 5.00,
        'manufacturing_cost' => 18.00,
        'prices' => [
            [
                'currency_id' => $this->eur->id,
                'price' => 41.86,
            ]
        ]
    ];

    $response = $this->postJson('/api/products', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('data.name', 'Wireless Mouse')
        ->assertJsonPath('data.price', 45.5)
        ->assertJsonPath('data.total_cost', 23);

    $this->assertDatabaseHas('products', [
        'name' => 'Wireless Mouse',
        'currency_id' => $this->usd->id,
    ]);

    $this->assertDatabaseHas('product_prices', [
        'currency_id' => $this->eur->id,
        'price' => 41.86,
    ]);
});

test('it validates required fields when creating a product', function () {
    $response = $this->postJson('/api/products', []);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'price', 'currency_id']);
});

test('it can show a specific product by id', function () {
    $product = Product::create([
        'name' => 'Headset Pro',
        'description' => '7.1 Surround',
        'price' => 120.00,
        'currency_id' => $this->usd->id,
        'tax_cost' => 12.00,
        'manufacturing_cost' => 45.00,
    ]);

    $response = $this->getJson("/api/products/{$product->id}");

    $response->assertStatus(200)
        ->assertJsonPath('data.id', $product->id)
        ->assertJsonPath('data.name', 'Headset Pro');
});

test('it can update a product', function () {
    $product = Product::create([
        'name' => 'Old Name',
        'description' => 'Old Desc',
        'price' => 50.00,
        'currency_id' => $this->usd->id,
    ]);

    $response = $this->putJson("/api/products/{$product->id}", [
        'name' => 'Updated Name',
        'price' => 65.00,
    ]);

    $response->assertStatus(200)
        ->assertJsonPath('data.name', 'Updated Name')
        ->assertJsonPath('data.price', 65);

    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Name',
        'price' => 65.00,
    ]);
});

test('it can delete a product', function () {
    $product = Product::create([
        'name' => 'To be deleted',
        'price' => 20.00,
        'currency_id' => $this->usd->id,
    ]);

    $response = $this->deleteJson("/api/products/{$product->id}");

    $response->assertStatus(200);
    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('it can list and register multiple currency prices for a product', function () {
    $product = Product::create([
        'name' => 'Multi-currency Product',
        'price' => 100.00,
        'currency_id' => $this->usd->id,
    ]);

    // Crear precio en EUR
    $postResponse = $this->postJson("/api/products/{$product->id}/prices", [
        'currency_id' => $this->eur->id,
        'price' => 92.00,
    ]);

    $postResponse->assertStatus(201)
        ->assertJsonPath('data.product_id', $product->id)
        ->assertJsonPath('data.currency_id', $this->eur->id)
        ->assertJsonPath('data.price', 92);

    // Listar precios
    $getResponse = $this->getJson("/api/products/{$product->id}/prices");

    $getResponse->assertStatus(200)
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.currency.symbol', 'EUR');
});

