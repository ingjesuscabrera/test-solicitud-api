<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Begento Healthcare Solutions - Backend Laravel API',
    description: 'API RESTful para la gestión de productos, costos y precios en diferentes divisas.',
    contact: new OA\Contact(name: 'Soporte Técnico', email: 'dev@begento.test')
)]
#[OA\Server(
    url: 'http://127.0.0.1:8000',
    description: 'Servidor Local de Desarrollo'
)]
#[OA\Server(
    url: 'http://localhost/test-laravel-api/public',
    description: 'Servidor XAMPP Apache'
)]
abstract class Controller
{
    //
}
