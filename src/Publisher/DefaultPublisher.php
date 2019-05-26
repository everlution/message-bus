<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Publisher;

use Everlution\MessageBus\Protocol\ProtocolInterface;
use Everlution\MessageBus\Serializer\SerializerInterface;
use Everlution\MessageBus\Transport\TransportInterface;
use Everlution\MessageBus\Validator\ValidatorInterface;

class DefaultPublisher implements PublisherInterface
{
    private $protocol;

    private $validator;

    private $serializer;

    private $transport;

    public function __construct(
        ProtocolInterface $protocol,
        ValidatorInterface $validator,
        SerializerInterface $serializer,
        TransportInterface $transport
    ) {
        $this->protocol = $protocol;
        $this->validator = $validator;
        $this->serializer = $serializer;
        $this->transport = $transport;
    }

    /**
     * @param array $data
     * @throws \Everlution\MessageBus\Validator\ValidatorException
     */
    public function publish(array $data): void
    {
        $this
            ->validator
            ->validate($this->protocol, $data);

        $message = $this
            ->serializer
            ->serialize($data);

        $this
            ->transport
            ->publish($message);
    }
}
