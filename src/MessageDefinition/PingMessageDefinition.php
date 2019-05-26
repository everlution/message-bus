<?php

declare(strict_types=1);

namespace Everlution\MessageBus\MessageDefinition;

class PingMessageDefinition implements MessageDefinitionInterface
{
    public static function getName(): string
    {
        return 'ping';
    }

    public function getJsonSchema(): string
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'messageName' => [
                    'type' => 'string',
                ],
                'messageBody' => [
                    'type' => 'object',
                    'properties' => [
                        'timestamp' => [
                            'type' => 'string',
                            'format' => 'date-time',
                        ],
                    ],
                    'additionalProperties' => false,
                    'required' => [
                        'timestamp',
                    ],
                ],
            ],
            'additionalProperties' => false,
            'required' => [
                'messageName',
                'messageBody',
            ],
        ];

        return json_encode($schema);
    }
}
