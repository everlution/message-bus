<?php

declare(strict_types=1);

namespace Everlution\Util\Symfony;

interface MessageBusEventInterface
{
    public function setName(string $name): void;

    public function getName(): ?string;

    public function setData(array $data): void;

    public function getData(): ?array;
}
