<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

use OpenApi\Attributes as OA;

#[OA\Tag(name: 'Divisas', description: 'Endpoints para el catálogo de divisas')]
class CurrencyController extends Controller
{
    /**
     * Display a listing of currencies.
     */
    #[OA\Get(
        path: '/api/currencies',
        summary: 'Obtener catálogo de divisas',
        tags: ['Divisas'],
        responses: [
            new OA\Response(response: 200, description: 'Catálogo de divisas obtenido exitosamente')
        ]
    )]
    public function index(): AnonymousResourceCollection
    {
        return CurrencyResource::collection(Currency::all());
    }

    /**
     * Store a newly created currency.
     */
    #[OA\Post(
        path: '/api/currencies',
        summary: 'Crear una nueva divisa',
        tags: ['Divisas'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'symbol', 'exchange_rate'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'British Pound'),
                    new OA\Property(property: 'symbol', type: 'string', example: 'GBP'),
                    new OA\Property(property: 'exchange_rate', type: 'number', format: 'float', example: 0.79)
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Divisa creada exitosamente'),
            new OA\Response(response: 422, description: 'Errores de validación')
        ]
    )]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'symbol' => ['required', 'string', 'max:10'],
            'exchange_rate' => ['required', 'numeric', 'min:0.0001'],
        ]);

        $currency = Currency::create($validated);

        return (new CurrencyResource($currency))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified currency.
     */
    #[OA\Get(
        path: '/api/currencies/{id}',
        summary: 'Obtener una divisa por ID',
        tags: ['Divisas'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, description: 'ID de la divisa', schema: new OA\Schema(type: 'integer'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Detalle de la divisa'),
            new OA\Response(response: 404, description: 'Divisa no encontrada')
        ]
    )]
    public function show(string $id): CurrencyResource
    {
        $currency = Currency::findOrFail($id);

        return new CurrencyResource($currency);
    }
}
