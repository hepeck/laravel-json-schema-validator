<?php

namespace Hepeck\Http;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationException;
use JsonSchema\Validator as JValidator;
use Illuminate\Validation\Validator;

class JsonSchemaValidator extends Validator
{
    private string $schema;
    private JValidator $validator;

    public function passes()
    {
        $this->messages = new MessageBag();

        $data = json_decode(json_encode($this->data));

        $this->getValidator()->validate($data, (object)['$ref' => "file://{$this->schema}"]);

        if ($this->getValidator()->isValid()) {
            return $this->messages->isEmpty();
        }

        foreach ($this->getValidator()->getErrors() as $error) {
            $this->messages()->add($error['property'], $error['message']);
        }

        return $this->messages->isEmpty();
    }

    public function setSchema(string $schema)
    {
        $this->schema = config('hepeck.schema_basepath', resource_path('schema')) . DIRECTORY_SEPARATOR . $schema;
    }

    public function getValidator(): JValidator
    {
        if (isset($this->validator)) {
            return $this->validator;
        }

        $this->validator = new JValidator();

        return $this->validator;
    }

    /**
     * Get the exception to throw upon failed validation.
     *
     * @return ValidationException
     */
    public function getException(): ValidationException
    {
        return new $this->exception($this);
    }
}
