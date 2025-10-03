[![Unit test execution](https://github.com/hepeck/laravel-json-schema-validator/actions/workflows/unittests.yml/badge.svg)](https://github.com/hepeck/laravel-json-schema-validator/actions/workflows/unittests.yml)
[![Create Release](https://github.com/hepeck/laravel-json-schema-validator/actions/workflows/release.yml/badge.svg)](https://github.com/hepeck/laravel-json-schema-validator/actions/workflows/release.yml)
[![codecov](https://codecov.io/github/hepeck/laravel-json-schema-validator/graph/badge.svg?token=H8D6C4GTY5)](https://codecov.io/github/hepeck/laravel-json-schema-validator)

# Laravel JSON Schema Validator

## Overview

The **Laravel JSON Schema Validator** package integrates [JSON Schema](https://json-schema.org/) validation into Laravel's request and validation system.  
It allows you to validate request payloads and data arrays against predefined JSON Schema files, providing strong guarantees for API contracts and data consistency.

This package is ideal for teams who want to share schemas between backend and frontend or enforce strict schema validation in Laravel projects.

---

## Installation

1. **Install the package** via Composer:

   ```bash
   composer require hepeck/laravel-json-schema-validator
   ```

2. **Publish configuration** (optional):

   ```bash
   php artisan vendor:publish --tag=json-schema-config
   ```

   This will create a `config/json-schema.php` file where you can configure the base path for your schema files.

---

## Usage

### 1. Create a JSON Schema

Example: `resources/schemas/user.json`

```json
{
  "$schema": "https://json-schema.org/draft/2020-12/schema",
  "title": "User",
  "type": "object",
  "required": ["name", "email", "age"],
  "properties": {
    "name": { "type": "string", "minLength": 2, "maxLength": 50 },
    "email": { "type": "string", "format": "email" },
    "age": { "type": "integer", "minimum": 18, "maximum": 120 }
  },
  "additionalProperties": false
}
```

---

### 2. Extend `JsonSchemaRequest`

```php
namespace App\Http\Requests;

use Hepeck\Http\Requests\JsonSchemaRequest;

class UserRequest extends JsonSchemaRequest
{
    protected string $schema = 'user.json';
}
```

---

### 3. Use in Controller

```php
namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;

class UserController extends Controller
{
    public function store(UserRequest $request)
    {
        // If validation passes, you can safely use the payload
        return response()->json([
            'message' => 'User validated successfully!',
            'data' => $request->validated()
        ]);
    }
}
```

---

## Configuration

By default, schemas are expected in:

```
resources/schemas/
```

You can override this in `config/json-schema.php`:

```php
return [
    'schema_basepath' => resource_path('schemas'),
];
```

---

## Artisan Commands

- **Validate a JSON file against a schema**:

   ```bash
   php artisan schema:validate user.json payload.json
   ```

This command validates a payload against the specified schema and prints the result in the console.

---

## Testing

This package includes PHPUnit tests. Run them with:

```bash
vendor/bin/phpunit -c unittests.xml
```

---

## License

This package is licensed under the **GNU Affero General Public License v3.0 or later (AGPL-3.0-or-later)**.  
See the [LICENSE](LICENSE) file for details.

---

## Conclusion

The **Laravel JSON Schema Validator** brings the power of JSON Schema validation into Laravel.  
It’s perfect for projects where data integrity and consistency matter, especially in API-driven architectures where schemas are shared across multiple systems.
