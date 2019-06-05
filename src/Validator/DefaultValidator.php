<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Validator;

use Everlution\MessageBus\Protocol\ProtocolInterface;
use JsonSchema\Validator;

class DefaultValidator implements ValidatorInterface
{
    private $validator;

    public function __construct(Validator $validator)
    {
        $this->validator = $validator;
    }

    /**
     * @param ProtocolInterface $protocol
     * @param array $message
     * @throws ValidatorException
     */
    public function validate(ProtocolInterface $protocol, array $message): void
    {
        $data = $this->arrayToObject($message);

        $this
            ->validator
            ->validate($data, json_decode($protocol->getJsonSchema(), true));

        if (!$this->validator->isValid()) {
            throw new ValidatorException(
                'JSON Schema validation failure',
                $protocol->getJsonSchema(),
                $message,
                $this->validator->getErrors()
            );
        }
    }

    private function arrayToObject($array)
    {
        if (count($array) == 0) {
            return new \stdClass();
        }

        // First we convert the array to a json string
        $json = json_encode($array);

        // The we convert the json string to a stdClass()
        $object = json_decode($json);

        return $object;
    }
}


