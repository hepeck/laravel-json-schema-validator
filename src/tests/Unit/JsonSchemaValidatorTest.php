<?php

namespace Hepeck\Tests\Unit;

use Hepeck\Http\JsonSchemaValidator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use PHPUnit\Framework\TestCase;

class JsonSchemaValidatorTest extends TestCase
{
    protected string $schemaPath;

    protected function setUp(): void
    {
        parent::setUp();

        $this->schemaPath = sys_get_temp_dir() . '/user-schema.json';

        file_put_contents($this->schemaPath, json_encode([
            '$schema' => "https://json-schema.org/draft/2020-12/schema",
            'type' => 'object',
            'properties' => [
                'name' => ['type' => 'string'],
                'age'  => ['type' => 'integer', 'minimum' => 18],
            ],
            'required' => ['name', 'age'],
            'additionalProperties' => false,
        ]));
    }

    public function test_valid_data_passes_validation()
    {
        $translator = new Translator(new ArrayLoader(), 'en');
        $validator = new JsonSchemaValidator(
            $translator,
            ['name' => 'Nino', 'age' => 25],
            [], [], []
        );

        $validator->setSchema(basename($this->schemaPath));

        $this->assertTrue($validator->passes());
        $this->assertTrue($validator->errors()->isEmpty());
    }

    public function test_invalid_data_fails_validation()
    {
        $translator = new Translator(new ArrayLoader(), 'en');
        $validator = new JsonSchemaValidator(
            $translator,
            ['name' => 'Nino', 'age' => 15],
            [], [], []
        );

        $validator->setSchema(basename($this->schemaPath));

        $this->assertFalse($validator->passes());
        $this->assertFalse($validator->errors()->isEmpty());
    }
}
