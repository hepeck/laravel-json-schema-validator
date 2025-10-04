<?php

namespace Hepeck\Providers;

use Hepeck\Http\Requests\JsonSchemaRequest;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class JsonSchemaRequestServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        app()->resolving(JsonSchemaRequest::class, function (JsonSchemaRequest $request, Application $app) {
            $request = JsonSchemaRequest::createFrom($app->get('request'), $request);
        });
    }
}
