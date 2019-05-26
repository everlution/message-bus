<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

$connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
    'rabbitmq_host',
    5672,
    'rabbituser',
    'password'
);
$exchange = 'MessageBus';
$consumerName = 'consumer1';

$transport = new \Everlution\MessageBus\Transport\RabbitMqTransport($connection, $exchange, $consumerName);

$pingMessage = new \Everlution\MessageBus\MessageDefinition\PingMessageDefinition();

$protocol = new \Everlution\MessageBus\Protocol\DefaultProtocol();
$protocol->addMessageDefinition($pingMessage);

$validator = new \Everlution\MessageBus\Validator\DefaultValidator();

$serializer = new \Everlution\MessageBus\Serializer\JsonSerializer();

$publisher = new Everlution\MessageBus\Publisher\DefaultPublisher(
    $protocol,
    $validator,
    $serializer,
    $transport
);

$consumer = new \Everlution\MessageBus\Consumer\EchoConsumer(
    $validator,
    $protocol,
    $serializer,
    $transport
);

$jsonSchemaDataGenerator = new \Everlution\MessageBus\Util\JsonSchemaDataGenerator();

$simulator = new \Everlution\MessageBus\Simulator\JsonSchemaFakerSimulator($jsonSchemaDataGenerator, $publisher);
