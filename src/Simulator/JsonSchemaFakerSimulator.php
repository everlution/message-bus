<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Simulator;

use Everlution\MessageBus\MessageDefinition\MessageDefinitionInterface;
use Everlution\MessageBus\Publisher\PublisherInterface;
use Everlution\MessageBus\Util\JsonSchemaDataGenerator;

class JsonSchemaFakerSimulator implements SimulatorInterface
{
    private $jsonSchemaDataGenerator;

    private $publisher;

    public function __construct(
        JsonSchemaDataGenerator $jsonSchemaDataGenerator,
        PublisherInterface $publisher
    ) {
        $this->jsonSchemaDataGenerator = $jsonSchemaDataGenerator;
        $this->publisher = $publisher;
    }

    /**
     * @param MessageDefinitionInterface $messageDefinition
     * @throws \Everlution\MessageBus\Validator\ValidatorException
     */
    public function simulate(MessageDefinitionInterface $messageDefinition): void
    {
        $data = $this
            ->jsonSchemaDataGenerator
            ->generate($messageDefinition->getJsonSchema());

        $this
            ->publisher
            ->publish($data);
    }
}
