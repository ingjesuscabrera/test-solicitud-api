# API RESTful de Gestión de Productos y Precios Multidivisa

API desarrollada con **Laravel 12** y **Eloquent ORM** para la gestión de productos, costos, y precios asociados en diferentes divisas, cumpliendo con todos los requisitos técnicos y estándares de arquitectura RESTful.

---

## 🛠 Requisitos y Tecnologías
- **PHP** >= 8.2
- **Laravel Framework** 12.x
- **MySQL** / MariaDB
- **Eloquent ORM**
- **Form Requests** para validaciones estrictas
- **API Resources** para transformación y desacoplamiento de respuestas JSON
- **Pest / PHPUnit** para pruebas funcionales y de integración

---

## 🚀 Instalación y Puesta en Marcha

1. **Configurar el entorno**:
   Asegúrate de que tu archivo `.env` apunte a tu base de datos MySQL local:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=test-api-laravel
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. **Ejecutar migraciones y seeders**:
   Puebla las divisas (USD, EUR, DOP, MXN) y productos de demostración:
   ```bash
   php artisan migrate:fresh --seed
   ```

3. **Iniciar el servidor local**:
   ```bash
   php artisan serve
   ```
   La API quedará escuchando en `http://127.0.0.1:8000`.

4. **Ejecutar pruebas automatizadas**:
   ```bash
   php artisan test
   ```

---

## 📋 Endpoints de la API

Todos los endpoints deben enviar la cabecera:
```http
Accept: application/json
Content-Type: application/json
```

### 1. Productos (`/api/products`)

| Método | Endpoint | Descripción |
| :--- | :--- | :--- |
| **GET** | `/api/products` | Obtener lista paginada de productos con su divisa base y precios. |
| **POST** | `/api/products` | Crear un nuevo producto (permite registrar precios en otras divisas en la misma petición). |
| **GET** | `/api/products/{id}` | Obtener el detalle de un producto por ID. |
| **PUT/PATCH** | `/api/products/{id}` | Actualizar un producto existente. |
| **DELETE** | `/api/products/{id}` | Eliminar un producto. |

#### Ejemplo de Payload: `POST /api/products`
```json
{
    "name": "Smart Watch Ultra",
    "description": "Reloj inteligente deportivo con GPS integrado",
    "price": 299.99,
    "currency_id": 1,
    "tax_cost": 21.00,
    "manufacturing_cost": 110.00,
    "prices": [
        {
            "currency_id": 2,
            "price": 275.99
        },
        {
            "currency_id": 3,
            "price": 18074.40
        }
    ]
}
```

---

### 2. Precios por Divisa (`/api/products/{id}/prices`)

| Método | Endpoint | Descripción |
| :--- | :--- | :--- |
| **GET** | `/api/products/{id}/prices` | Obtener todos los precios registrados para el producto en diferentes divisas. |
| **POST** | `/api/products/{id}/prices` | Registrar o actualizar el precio del producto en una divisa específica. |

#### Ejemplo de Payload: `POST /api/products/{id}/prices`
```json
{
    "currency_id": 4,
    "price": 61232.50
}
```

---

### 3. Divisas (`/api/currencies`)

| Método | Endpoint | Descripción |
| :--- | :--- | :--- |
| **GET** | `/api/currencies` | Obtener catálogo de divisas registradas. |
| **POST** | `/api/currencies` | Crear una nueva divisa (ej. `GBP`, `CAD`). |
| **GET** | `/api/currencies/{id}` | Obtener una divisa por ID. |

---

## 🧪 Pruebas con Postman

Se incluye en la raíz del repositorio el archivo:
- `postman_collection.json`

### Cómo importarlo:
1. Abre Postman.
2. Haz clic en **Import** y selecciona el archivo `postman_collection.json`.
3. Todas las variables (como `{{base_url}} = http://127.0.0.1:8000`) y payloads de ejemplo ya están configurados listos para ejecutar.
