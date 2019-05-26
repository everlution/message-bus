<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Validator;

class ValidatorException extends \Exception
{
    private $jsonSchema;

    private $data;

    private $errors;

    public function __construct(
        string $message = "",
        string $jsonSchema,
        array $data,
        array $errors
    ) {
        parent::__construct($message);

        $this->jsonSchema = $jsonSchema;
        $this->data = $data;
        $this->errors = $errors;
    }

    public function getJsonSchema(): string
    {
        return $this->jsonSchema;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
