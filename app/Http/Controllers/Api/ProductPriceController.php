<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductPriceController extends Controller
{
    /**
     * Get list of prices for a specific product.
     * GET /products/{id}/prices
     */
    public function index(string $productId): AnonymousResourceCollection
    {
        $product = Product::findOrFail($productId);
        $prices = $product->prices()->with('currency')->get();

        return ProductPriceResource::collection($prices);
    }

    /**
     * Store or update a price for a specific product in a given currency.
     * POST /products/{id}/prices
     */
    public function store(StoreProductPriceRequest $request, string $productId): JsonResponse
    {
        $product = Product::findOrFail($productId);
        $data = $request->validated();

        $price = $product->prices()->updateOrCreate(
            ['currency_id' => $data['currency_id']],
            ['price' => $data['price']]
        );

        $price->load('currency');

        return (new ProductPriceResource($price))
            ->response()
            ->setStatusCode(201);
    }
}
