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

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Productos', description: 'Endpoints para la gestión de productos y costos')]
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    #[OA\Get(
        path: '/api/products',
        summary: 'Obtener lista de productos paginada',
        tags: ['Productos'],
        responses: [
            new OA\Response(response: 200, description: 'Lista de productos obtenida exitosamente')
        ]
    )]
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
    #[OA\Post(
        path: '/api/products',
        summary: 'Crear un nuevo producto',
        tags: ['Productos'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'currency_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro 15"'),
                    new OA\Property(property: 'description', type: 'string', example: 'Laptop con procesador Octa-Core y 16GB RAM'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1299.99),
                    new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tax_cost', type: 'number', format: 'float', example: 90.99),
                    new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float', example: 650.00),
                    new OA\Property(
                        property: 'prices',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property(property: 'currency_id', type: 'integer', example: 2),
                                new OA\Property(property: 'price', type: 'number', format: 'float', example: 1195.99)
                            ]
                        )
                    )
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Producto creado exitosamente'),
            new OA\Response(response: 422, description: 'Errores de validación')
        ]
    )]
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
    #[OA\Get(
        path: '/api/products/{id}',
        summary: 'Obtener un producto por ID',
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID del producto', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Detalle del producto'),
            new OA\Response(response: 404, description: 'Producto no encontrado')
        ]
    )]
    public function show(string $id): ProductResource
    {
        $product = Product::with(['currency', 'prices.currency'])->findOrFail($id);

        return new ProductResource($product);
    }

    /**
     * Update the specified resource in storage.
     */
    #[OA\Put(
        path: '/api/products/{id}',
        summary: 'Actualizar un producto por ID',
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID del producto', schema: new OA\Schema(type: 'integer'))
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro 15" Actualizada'),
                    new OA\Property(property: 'description', type: 'string', example: 'Nueva descripción'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 1399.99),
                    new OA\Property(property: 'currency_id', type: 'integer', example: 1),
                    new OA\Property(property: 'tax_cost', type: 'number', format: 'float', example: 98.00),
                    new OA\Property(property: 'manufacturing_cost', type: 'number', format: 'float', example: 700.00)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 200, description: 'Producto actualizado exitosamente'),
            new OA\Response(response: 404, description: 'Producto no encontrado'),
            new OA\Response(response: 422, description: 'Errores de validación')
        ]
    )]
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
    #[OA\Delete(
        path: '/api/products/{id}',
        summary: 'Eliminar un producto por ID',
        tags: ['Productos'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID del producto', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Producto eliminado exitosamente'),
            new OA\Response(response: 404, description: 'Producto no encontrado')
        ]
    )]
    public function destroy(string $id): JsonResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return response()->json([
            'message' => 'Producto eliminado exitosamente.'
        ], 200);
    }
}
