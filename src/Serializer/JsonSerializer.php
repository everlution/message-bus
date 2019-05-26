<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Serializer;

class JsonSerializer implements SerializerInterface
{
    public function serialize(array $message): string
    {
        return json_encode($message);
    }

    public function deserialize(string $message): array
    {
        return json_decode($message, true);
    }
}
