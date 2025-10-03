<?php

namespace Hepeck\Http\Requests;

use Hepeck\Http\JsonSchemaValidator;
use Illuminate\Contracts\Validation\ValidatesWhenResolved;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\Validator;

class JsonSchemaRequest extends Request implements ValidatesWhenResolved
{

    /**
     * The validator instance.
     *
     * @var Validator
     */
    protected Validator $validator;

    /**
     * The redirector instance.
     *
     * @var \Illuminate\Routing\Redirector
     */
    protected \Illuminate\Routing\Redirector $redirector;

    protected string $schema;

    public function validateResolved()
    {
        if (!$this->authorize()) {
            throw new UnauthorizedException;
        }

        $instance = $this->validator();


        if ($instance->fails()) {
            $this->failedValidation($instance);
        }
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(JsonSchemaValidator $validator)
    {
        throw $validator->getException()
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function setValidator(Validator $validator): void
    {
        $this->validator = $validator;
    }

    public function validator(): Validator
    {
        if (isset($this->validator)) {
            return $this->validator;
        }

        $validator = new JsonSchemaValidator(
            app('translator'),
            $this->all(),
            $this->rules(),
            $this->messages(),
            $this->attributes(),
        );
        $validator->setSchema($this->schema);

        $this->setValidator($validator);

        return $this->validator;
    }

    public function rules(): array
    {
        return [];
    }

    public function messages(): array
    {
        return [];
    }

    public function attributes()
    {
        return [];
    }

    protected function getRedirectUrl()
    {
        $url = $this->getRedirector()->getUrlGenerator();

        return $url->previous();
    }

    public function getRedirector(): \Illuminate\Routing\Redirector
    {
        if (isset($this->redirector)) {
            return $this->redirector;
        }

        $this->redirector = app(Redirector::class);

        return $this->redirector;
    }
}
