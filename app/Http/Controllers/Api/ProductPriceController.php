<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Precios de Productos', description: 'Endpoints para la gestión de precios en diferentes divisas')]
class ProductPriceController extends Controller
{
    /**
     * Get list of prices for a specific product.
     * GET /products/{id}/prices
     */
    #[OA\Get(
        path: '/api/products/{id}/prices',
        summary: 'Obtener lista de precios de un producto en diferentes divisas',
        tags: ['Precios de Productos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID del producto', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Lista de precios obtenida exitosamente'),
            new OA\Response(response: 404, description: 'Producto no encontrado')
        ]
    )]
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
    #[OA\Post(
        path: '/api/products/{id}/prices',
        summary: 'Registrar o actualizar el precio de un producto en una divisa',
        tags: ['Precios de Productos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID del producto', schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['currency_id', 'price'],
                properties: [
                    new OA\Property(property: 'currency_id', type: 'integer', example: 2),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 95.50)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Precio registrado/actualizado exitosamente'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
            new OA\Response(response: 422, description: 'Errores de validación')
        ]
    )]
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
