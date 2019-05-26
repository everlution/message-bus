<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Protocol;

use Everlution\MessageBus\MessageDefinition\MessageDefinitionInterface;

interface ProtocolInterface
{
    public function addMessageDefinition(MessageDefinitionInterface $messageDefinition): void;

    public function getMessageDefinitions(): array;

    public function getJsonSchema(): string;
}
