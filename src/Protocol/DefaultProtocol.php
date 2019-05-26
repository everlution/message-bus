<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Protocol;

use Everlution\MessageBus\MessageDefinition\MessageDefinitionInterface;

class DefaultProtocol implements ProtocolInterface
{
    /** @var MessageDefinitionInterface[] */
    private $messageDefinitions;

    public function __construct()
    {
        $this->messageDefinitions = [];
    }

    public function addMessageDefinition(MessageDefinitionInterface $messageDefinition): void
    {
        $this->messageDefinitions[] = $messageDefinition;
    }

    public function getMessageDefinitions(): array
    {
        return $this->messageDefinitions;
    }

    public function getJsonSchema(): string
    {
        $schema = [
            'title' => 'The message bus protocol',
            'type' => 'object',
            'oneOf' => [],
        ];

        foreach ($this->messageDefinitions as $messageDefinition) {
            $schema['oneOf'][] = json_decode($messageDefinition->getJsonSchema(), true);
        }

        return json_encode($schema);
    }
}
