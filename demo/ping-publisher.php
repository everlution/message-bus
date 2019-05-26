<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

$message = [
    'messageName' => $pingMessage::getName(),
    'messageBody' => [
        'timestamp' => date('c'),
    ],
];

try {
    $publisher->publish($message);
} catch (\Everlution\MessageBus\Validator\ValidatorException $e) {
    var_dump($e->getJsonSchema());
    var_dump($e->getData());
    var_dump($e->getErrors());
}
