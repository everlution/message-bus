<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Validator;

use Everlution\MessageBus\Protocol\ProtocolInterface;
use Opis\JsonSchema\Schema;
use Opis\JsonSchema\Validator;

class DefaultValidator implements ValidatorInterface
{
    /**
     * @param ProtocolInterface $protocol
     * @param array $message
     * @throws ValidatorException
     */
    public function validate(ProtocolInterface $protocol, array $message): void
    {
        $schema = Schema::fromJsonString($protocol->getJsonSchema());

        $validator = new Validator();

        $object = json_decode(json_encode($message));

        $result = $validator->schemaValidation($object, $schema);

        if (!$result->isValid()) {
            $errors = [];

            foreach ($result->getErrors() as $error) {
                $errors[$error->keyword()] = json_encode($error->keywordArgs());
            }

            throw new ValidatorException(
                'JSON Schema validation failure',
                $protocol->getJsonSchema(),
                $message,
                $errors
            );
        }
    }
}
