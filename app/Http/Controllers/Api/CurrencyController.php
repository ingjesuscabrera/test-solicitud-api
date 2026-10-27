<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CurrencyResource;
use App\Models\Currency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CurrencyController extends Controller
{
    /**
     * Display a listing of currencies.
     */
    public function index(): AnonymousResourceCollection
    {
        return CurrencyResource::collection(Currency::all());
    }

    /**
     * Store a newly created currency.
     */
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
    public function show(string $id): CurrencyResource
    {
        $currency = Currency::findOrFail($id);

        return new CurrencyResource($currency);
    }
}
