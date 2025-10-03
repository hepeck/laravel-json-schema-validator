<?php

namespace Hepeck\Tests\Unit;

use Hepeck\app\Providers\JsonSchemaRequestServiceProvider;
use Hepeck\Http\Requests\JsonSchemaRequest;
use Illuminate\Container\Container;
use PHPUnit\Framework\TestCase;

class JsonSchemaRequestServiceProviderTest extends TestCase
{
    public function test_service_provider_binds_request()
    {
        $app = new Container();
        $provider = new JsonSchemaRequestServiceProvider($app);
        $provider->boot();

        $resolved = $app->make(JsonSchemaRequest::class);

        $this->assertInstanceOf(JsonSchemaRequest::class, $resolved);
    }
}
