# Everlution Eventbus

This library provides an eventbus solution in PHP for making microservices interact together.

## Installation

As usual just use composer as follows

```
composer require everlution\message-bus
```

## Components

This library provides default components but you can easily develop your owns by implementing the proper interfaces. 

### Transport

Must implement `Everlution\MessageBus\Transport\TransportInterface`.

This component is the actual service that interacts with the "physical" message bus. In other words it is the implementation for using a specific technology.

This library provides already a transport for RabbitMQ `Everlution\MessageBus\Transport\RabbitMqTransport` using a fanout 
exchange and re-publishing the message in case of any exception thrown within your consumer.

```php
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Everlution\MessageBus\Transport\RabbitMqTransport;

$connection = new AMQPStreamConnection('rabbitmq_host', 5672, 'user', 'password');
$exchange = 'MessageBus';
$consumerName = 'consumer1';

$transport = new RabbitMqTransport($connection, $exchange, $consumerName);
```

### Message Definition

This is the actual message that will be published on the Transport.

A message can only be an array that will be validated using a JSON Schema definition. 
In fact the message definition requires you to provide:
- the name of the message (as a discriminator)
- the JSON Schema that validates the message payload itself

The library provides the `Everlution\MessageBus\MessageDefinition\PingMessageDefinition` for testing purposes.

```php
<?php

class SmsSendMessage implements MessageDefinitionInterface
{
    public static function getName(): string
    {
        return 'sms_send';
    }

    public function getJsonSchema(): string
    {
        $schema = [
            'type' => 'object',
            'properties' => [
                'messageName' => [
                    'type' => 'string',
                    'enum' => [self::getName()],
                ],
                'number' => [
                    'type' => 'string',
                    'regex' => '$\d*^'
                ],
                'text' => [
                    'type' => 'string',
                    'maxLength' => 255,
                ],
            ],
            'additionalProperties' => false,
            'required' => [
                'messageName',
                'number',
                'text',
            ],
        ];

        return json_encode($schema);
    }
}

```

So in order to define what consumers can publish on the message bus you need to create your messages definitions and then add them to the `Protocol`.

You can literally shape the message definition as you want as this library doesn't force you to follow any constraint.

### Protocol

The `Protocol` defines in fact the protocol used by all the consumers of the message bus for describing the structure of 
all the available messages that can be published on the `Transport`.

A Protocol must implement the `$protocol->addMessageDefinition()` method that you need to call for every message 
definition you want to use within your environment. 

It basically represents not only a registry of message definitions.

This library provides a default Protocol `Everlution\MessageBus\Protocol\DefaultProtocol` which basically allows
publishers to publish only **one of** the messages that match one of the message definitions added to the Protocol.

```php
use Everlution\MessageBus\Protocol\DefaultProtocol;

$protocol = new DefaultProtocol();
$protocol->addMessageDefinition(new SmsSendMessage());
```

### Validator

This component is in charge of validating a message against the Protocol.

The `Everlution\MessageBus\Validator\DefaultValidator` provided uses the `Opis\JsonSchema` library for that but you can
develop your own validation service by implementing `Everlution\MessageBus\Validator\ValidatorInterface`.

```php
use Everlution\MessageBus\Validator\DefaultValidator;
use Everlution\MessageBus\Validator\ValidatorException;

$validator = new DefaultValidator();

$message = [
    'messageName' => 'sms_send',
    'number' => '0777777777',
    'text' => 'This is my message,
];

try {
    $validator->validate($protocol, $message);
} catch (ValidatorException $e) {
    var_dump($e->getJsonSchema());
    var_dump($e->getData());
    var_dump($e->getErrors());
}
```

### Serializer

The Serializer clearly transforms the message array you want to publish on the Transport into a string and viceversa.

The library provides already the `Everlution\MessageBus\Serializer\JsonSerializer`.

### Publisher

The Publisher must implement `Everlution\MessageBus\Publisher\PublisherInterface` and its task is to publish the 
message on the Transport.

The `Everlution\MessageBus\Publisher\DefaultPublisher` provided does the following steps:
1. validates the message against the Protocol
2. serializes the message
3. publishes the message on the Transport 

```php
use Everlution\MessageBus\Publisher\DefaultPublisher;

$publisher = new DefaultPublisher($protocol, $validator, $serializer, $transport);

try {
    $publisher->publish($message);
} catch (\Everlution\MessageBus\Validator\ValidatorException $e) {
    var_dump($e->getJsonSchema());
    var_dump($e->getData());
    var_dump($e->getErrors());
}
```

### Consumer

The Consumer must implement `Everlution\MessageBus\Consumer\ConsumerInterface` and defines the business logic within 
your application of how you want to consume messages.

This library provides two consumers.

#### Echo Consumer

This consumer `Everlution\MessageBus\Consumer\EchoConsumer` is just for testing purposes and basically does a 
`var_dump(message)`.

```php
use Everlution\MessageBus\Consumer\EchoConsumer;

$consumer = new EchoConsumer($validator, $protocol, $serializer, $transport);

try {
    $consumer->consume($numberOfMessagesToConsumeAntThenExit = 1);
} catch (\Everlution\MessageBus\Validator\ValidatorException $e) {
    var_dump($e->getJsonSchema());
    var_dump($e->getData());
    var_dump($e->getErrors());
}
```

### Symfony Event Dispatcher Consumer

This consumer `Everlution\MessageBus\Consumer\SymfonyEventDispatcherConsumer` integrates with Symfony and allows you to 
transform a normal message into a Symfony Event and dispatch it within your application. In this way you can simply 
define an event subscriber to that and implement all the logic you need. 

This concept decouples the message bus from you application as event subscribers don't who generated the event.

### Simulator

It is very likely that if you are using this library you are working with microservices. If this is so you probably
don't want to have all the microservices up and running in your local environment in order to generate messages on the 
bus. On top of that it would be even hard to generate the messages that you need.

The Simulator helps you out as you only need to run locally your microservice and use the Simulator to generate only the
messages that you need as if they would come from other sources.

This concept is really useful as it saves you so much time and allows you to concentrate only on that microservice.

This library provides `Everlution\MessageBus\Simulator\JsonSchemaFakerSimulator` that uses the JSON Schema of the message 
that you want to simulate and using the Faker library it generates and publish a valid message. This service doesn't
cover up all the JSON Schemas you may define but it's a good starting point.

You can extend it or implement your own Simulator by implementing `Everlution\MessageBus\Simulator\SimulatorInterface`.

## Demo

You can find some working examples in `/demo`.

You can run them using docker with the following commands:

```bash
# running composer
./docker/bin/composer.sh "install"

# docker-compose up which spins up rabbitmq etc
./docker/bin/compose/up.sh

# You can ssh in the php-cli container
./docker/bin/ssh.sh php-cli

# You can now run the demo scripts
php demo/ping-publisher.php
php demo/pint-consumer.php

# When you are done tear everything down and destroy all
./docker/bin/compose/down.sh
./docker/bin/compose/rm.sh
```
