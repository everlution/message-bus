<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Simulator;

use Everlution\MessageBus\MessageDefinition\MessageDefinitionInterface;

interface SimulatorInterface
{
    public function simulate(MessageDefinitionInterface $messageDefinition): void;
}
