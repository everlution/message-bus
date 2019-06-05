<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Consumer;

use Everlution\MessageBus\Util\Symfony\MessageBusEventInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Everlution\MessageBus\Protocol\ProtocolInterface;
use Everlution\MessageBus\Serializer\SerializerInterface;
use Everlution\MessageBus\Transport\TransportInterface;
use Everlution\MessageBus\Validator\ValidatorInterface;

class SymfonyEventDispatcherConsumer implements ConsumerInterface
{
    private $eventDispatcher;

    private $validator;

    private $protocol;

    private $serializer;

    private $transport;

    private $symfonyEventClassName;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ValidatorInterface $validator,
        ProtocolInterface $protocol,
        SerializerInterface $serializer,
        TransportInterface $transport,
        string $symfonyEventClassName
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
        $this->protocol = $protocol;
        $this->serializer = $serializer;
        $this->transport = $transport;
        $this->symfonyEventClassName = $symfonyEventClassName;
    }

    public function consume(int $numberOfMessages): void
    {
        if (!is_a($this->symfonyEventClassName, MessageBusEventInterface::class, true)) {
            $msg = sprintf(
                'Symfony event of class %s must implement %s',
                $this->symfonyEventClassName,
                MessageBusEventInterface::class
            );

            throw new \Exception($msg);
        }

        $this
            ->transport
            ->consume([$this, 'logic'], $numberOfMessages);
    }

    public function logic(string $message)
    {
        $data = $this
            ->serializer
            ->deserialize($message);

        $this
            ->validator
            ->validate($this->protocol, $data);

        if (!isset($data['messageName'])) {
            throw new \Exception('Message Name not found');
        }

        $eventName = $data['messageName'];

        /** @var MessageBusEventInterface $event */
        $event = new $this->symfonyEventClassName;
        $event->setName($eventName);
        $event->setData($data);

        $this
            ->eventDispatcher
            ->dispatch($eventName, $event);
    }
}
