<?php

namespace HepeckTests\Unit;

use Hepeck\Http\JsonSchemaValidator;
use Hepeck\Http\Requests\JsonSchemaRequest;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use PHPUnit\Framework\TestCase;

class JsonSchemaRequestTest extends TestCase
{
    private JsonSchemaValidator $validator;

    protected function setUp(): void
    {
        parent::setUp();

        $this->validator = $this->createMock(JsonSchemaValidator::class);
    }

    protected function makeRequest(array $data): JsonSchemaRequest
    {
        $request = new JsonSchemaRequest($data);

        $request->setValidator($this->validator);

        return $request;
    }

    public function test_valid_request_passes()
    {
        $request = new JsonSchemaRequest(['email' => 'test@example.com']);

        $request->setValidator($this->validator);

        $this->expectNotToPerformAssertions();

        $this->validator->method('fails')->willReturn(false);

        $request->validateResolved();
    }

    public function test_invalid_request_throws_exception()
    {
        $request = $this->createPartialMock(JsonSchemaRequest::class, ['getRedirectUrl', 'validator']);

        $this->validator->method('fails')->willReturn(true);

        $messageBag = $this->createMock(MessageBag::class);
        $messageBag->method('all')->willReturn(['Some random error']);

        $this->validator->method('errors')->willReturn($messageBag);
        $this->validator->method('getException')->willReturn(new ValidationException($this->validator));
        $request->expects($this->once())->method('getRedirectUrl')->willReturn('/');
        $request->expects($this->once())->method('validator')->willReturn($this->validator);

        $this->expectException(ValidationException::class);
        $request->validateResolved();
    }
}
