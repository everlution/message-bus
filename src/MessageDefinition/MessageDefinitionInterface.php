<?php

declare(strict_types=1);

namespace Everlution\MessageBus\MessageDefinition;

interface MessageDefinitionInterface
{
    public static function getName(): string;

    public function getJsonSchema(): string;
}
