<?php

namespace HepeckTests\Unit;

use Hepeck\Http\JsonSchemaValidator;
use Hepeck\Http\Requests\JsonSchemaRequest;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

class JsonSchemaRequestTest extends TestCase
{
    protected function makeRequest(array $data, string $schemaPath): JsonSchemaRequest
    {
        $request = new class($data) extends JsonSchemaRequest {
            public function __construct(private array $data) {}
            public function all($keys = null) { return $this->data; }
        };

        $translator = new Translator(new ArrayLoader(), 'en');
        $validator = new JsonSchemaValidator($translator, $data, [], [], []);
        $validator->setSchema($schemaPath);

        $request->setValidator($validator);

        return $request;
    }

    public function test_valid_request_passes()
    {
        $schemaPath = sys_get_temp_dir() . '/contact-schema.json';
        file_put_contents($schemaPath, json_encode([
            'type' => 'object',
            'properties' => ['email' => ['type' => 'string', 'format' => 'email']],
            'required' => ['email']
        ]));

        $request = $this->makeRequest(['email' => 'test@example.com'], $schemaPath);

        $this->expectNotToPerformAssertions();
        $request->validateResolved();
    }

    public function test_invalid_request_throws_exception()
    {
        $schemaPath = sys_get_temp_dir() . '/contact-schema.json';
        file_put_contents($schemaPath, json_encode([
            'type' => 'object',
            'properties' => ['email' => ['type' => 'string', 'format' => 'email']],
            'required' => ['email']
        ]));

        $request = $this->makeRequest(['email' => 'not-an-email'], $schemaPath);

        $this->expectException(ValidationException::class);
        $request->validateResolved();
    }
}
