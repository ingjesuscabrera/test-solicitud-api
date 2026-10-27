<div align="center">

# 📦 API RESTful de Gestión de Productos y Precios Multidivisa
### Prueba Técnica Backend - Begento Healthcare Solutions

[![Laravel](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Swagger](https://img.shields.io/badge/Swagger-OpenAPI%203.0-85EA2D?style=for-the-badge&logo=swagger&logoColor=black)](http://127.0.0.1:8000/api/documentation)
[![Postman](https://img.shields.io/badge/Postman-v2.1-FF6C37?style=for-the-badge&logo=postman&logoColor=white)](https://www.postman.com/)
[![Tests](https://img.shields.io/badge/Tests-Pest%20%2F%20Passing-success?style=for-the-badge&logo=pest)](https://pestphp.com/)

---

### 👨‍💻 Candidato & Datos de Contacto
**Nombre:** Jesús Cabrera  
**Puesto al que aplica:** Backend Developer (Laravel)  
**Email:** [ingjesuscabrera@gmail.com](mailto:ingjesuscabrera@gmail.com)  
**GitHub:** [github.com/ingjesuscabrera](https://github.com/ingjesuscabrera)  

---

</div>

## 📌 Descripción del Proyecto

API RESTful de alto rendimiento desarrollada en **Laravel 12** y **Eloquent ORM** para la administración de productos, desglose de costos (impuestos, manufactura, costo total) y gestión de precios dinámicos en diferentes divisas.

El proyecto fue diseñado aplicando buenas prácticas de ingeniería de software:
- **Clean Architecture & SOLID principles**
- **Form Requests** para validaciones estrictas y desacopladas
- **API Resources (Transformers)** para estandarización y serialización JSON consistente
- **Database Transactions (`DB::transaction`)** para garantizar atomicidad e integridad referencial
- **Swagger / OpenAPI 3.0** integrado con interfaz interactiva para pruebas en vivo
- **Suite de Pruebas Automatizadas (Pest PHP)** con 100% de cobertura en los endpoints críticos

---

## 🛠️ Tecnologías y Dependencias

* **Framework:** Laravel 12.x
* **Lenguaje:** PHP 8.2+
* **Base de Datos:** MySQL / MariaDB (con soporte para migraciones y seeders)
* **ORM:** Eloquent ORM
* **Documentación Interactiva:** L5-Swagger (OpenAPI 3.0 / Swagger UI)
* **Testing:** Pest PHP / PHPUnit

---

## 🚀 Instalación y Puesta en Marcha Local

### 1. Clonar el repositorio
```bash
git clone https://github.com/ingjesuscabrera/test-laravel-api.git
cd test-laravel-api
```

### 2. Instalar dependencias
```bash
composer install
```

### 3. Configurar variables de entorno
Copia el archivo `.env.example` y ajusta las credenciales de tu base de datos:
```bash
cp .env.example .env
php artisan key:generate
```

Ejemplo de configuración en `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=test-api-laravel
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Ejecutar migraciones y datos de prueba (Seeders)
Este comando creará las tablas y sembrará divisas iniciales (USD, EUR, DOP, MXN) junto a productos de ejemplo:
```bash
php artisan migrate:fresh --seed
```

### 5. Iniciar el servidor local
```bash
php artisan serve
```
La aplicación quedará disponible en: `http://127.0.0.1:8000`

---

## 📖 Documentación Interactiva (Swagger UI)

Una vez iniciado el servidor, puedes interactuar y probar todos los endpoints en tiempo real desde tu navegador:

👉 **[http://127.0.0.1:8000/api/documentation](http://127.0.0.1:8000/api/documentation)**

> Si realizas modificaciones en los controladores o anotaciones, puedes regenerar la documentación con:
> ```bash
> php artisan l5-swagger:generate
> ```

---

## 📮 Colección de Postman

Se incluye en la raíz del repositorio el archivo con la colección completa lista para importar:
* 📄 **[`postman_collection.json`](postman_collection.json)**

### Pasos para usar en Postman:
1. Abre Postman y haz clic en **Import**.
2. Selecciona o arrastra el archivo `postman_collection.json`.
3. La variable de entorno `{{base_url}}` viene preconfigurada con `http://127.0.0.1:8000`.

---

## 📋 Catálogo de Endpoints

Todas las solicitudes deben incluir los siguientes headers HTTP:
```http
Accept: application/json
Content-Type: application/json
```

### 🔹 1. Productos (`/api/products`)

| Método | Endpoint | Descripción |
| :---: | :--- | :--- |
| `GET` | `/api/products` | Lista paginada de productos con su divisa base, costos y precios adicionales. |
| `POST` | `/api/products` | Registra un nuevo producto (permite registrar precios en múltiples monedas simultáneamente). |
| `GET` | `/api/products/{id}` | Obtiene el detalle de un producto específico por ID. |
| `PUT` | `/api/products/{id}` | Actualiza parcialmente o totalmente un producto existente. |
| `DELETE` | `/api/products/{id}` | Elimina un producto de la base de datos. |

#### Ejemplo Payload: `POST /api/products`
```json
{
  "name": "Smart Watch Ultra",
  "description": "Reloj inteligente deportivo con pantalla AMOLED y GPS integrado",
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

### 🔹 2. Precios por Divisa (`/api/products/{id}/prices`)

| Método | Endpoint | Descripción |
| :---: | :--- | :--- |
| `GET` | `/api/products/{id}/prices` | Lista todos los precios registrados para el producto en divisas secundarias. |
| `POST` | `/api/products/{id}/prices` | Registra o actualiza el precio de un producto para una divisa en particular. |

#### Ejemplo Payload: `POST /api/products/{id}/prices`
```json
{
  "currency_id": 4,
  "price": 61232.50
}
```

---

### 🔹 3. Catálogo de Divisas (`/api/currencies`)

| Método | Endpoint | Descripción |
| :---: | :--- | :--- |
| `GET` | `/api/currencies` | Obtiene el listado de todas las monedas y sus tipos de cambio. |
| `POST` | `/api/currencies` | Registra una nueva divisa en el sistema. |
| `GET` | `/api/currencies/{id}` | Obtiene los detalles de una divisa en particular. |

---

## 🧪 Pruebas Automatizadas

El proyecto cuenta con pruebas automáticas de integración que validan los flujos CRUD, la integridad de la base de datos y los códigos de estado HTTP:

```bash
php artisan test
```

### Resultados de los Tests:
```text
  PASS  Tests\Unit\ExampleTest
  ✓ that true is true

  PASS  Tests\Feature\ExampleTest
  ✓ it returns a successful response

  PASS  Tests\Feature\ProductApiTest
  ✓ it can list products with pagination and relations
  ✓ it can create a product with valid data
  ✓ it validates required fields when creating a product
  ✓ it can show a specific product by id
  ✓ it can update a product
  ✓ it can delete a product
  ✓ it can list and register multiple currency prices for a product

  Tests:    9 passed (46 assertions)
```

---

## 👤 Autor

* **Jesús Cabrera** - *Backend Developer*  
* Email: [ingjesuscabrera@gmail.com](mailto:ingjesuscabrera@gmail.com)  
* GitHub: [@ingjesuscabrera](https://github.com/ingjesuscabrera)
