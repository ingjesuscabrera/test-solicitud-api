<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): AnonymousResourceCollection
    {
        $products = Product::with(['currency', 'prices.currency'])
            ->latest('id')
            ->paginate(15);

        return ProductResource::collection($products);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = DB::transaction(function () use ($request) {
            $data = $request->validated();
            
            $product = Product::create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'currency_id' => $data['currency_id'],
                'tax_cost' => $data['tax_cost'] ?? 0,
                'manufacturing_cost' => $data['manufacturing_cost'] ?? 0,
            ]);

            // Si vienen precios adicionales por divisa en la creación
            if (!empty($data['prices'])) {
                foreach ($data['prices'] as $priceItem) {
                    $product->prices()->updateOrCreate(
                        ['currency_id' => $priceItem['currency_id']],
                        ['price' => $priceItem['price']]
                    );
                }
            }

            return $product;
        });

        $product->load(['currency', 'prices.currency']);

        return (new ProductResource($product))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): ProductResource
    {
        $product = Product::with(['currency', 'prices.currency'])->findOrFail($id);

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, string $id): ProductResource
    {
        $product = Product::findOrFail($id);

        $product->update($request->validated());
        $product->load(['currency', 'prices.currency']);

        return new ProductResource($product);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente.'
        ], 200);
    }
}
