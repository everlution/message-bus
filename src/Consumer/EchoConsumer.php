<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Consumer;

use Everlution\MessageBus\Protocol\ProtocolInterface;
use Everlution\MessageBus\Serializer\SerializerInterface;
use Everlution\MessageBus\Transport\TransportInterface;
use Everlution\MessageBus\Validator\ValidatorException;
use Everlution\MessageBus\Validator\ValidatorInterface;

class EchoConsumer implements ConsumerInterface
{
    private $validator;

    private $protocol;

    private $serializer;

    private $transport;

    public function __construct(
        ValidatorInterface $validator,
        ProtocolInterface $protocol,
        SerializerInterface $serializer,
        TransportInterface $transport
    ) {
        $this->validator = $validator;
        $this->protocol = $protocol;
        $this->serializer = $serializer;
        $this->transport = $transport;
    }

    /**
     * @param int $numberOfMessages
     * @throws ValidatorException
     */
    public function consume(int $numberOfMessages): void
    {
        $this
            ->transport
            ->consume([$this, 'logic'], $numberOfMessages);
    }

    public function logic(string $message): void
    {
        $data = $this
            ->serializer
            ->deserialize($message);

        $this
            ->validator
            ->validate($this->protocol, $data);

        var_dump($data);
    }
}
