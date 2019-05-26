<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Transport;

interface TransportInterface
{
    public function publish(string $payload): void;

    public function consume(callable $callback, int $numberOfMessages): void;
}
