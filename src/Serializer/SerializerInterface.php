<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Serializer;

interface SerializerInterface
{
    public function serialize(array $message): string;

    public function deserialize(string $message): array;
}
