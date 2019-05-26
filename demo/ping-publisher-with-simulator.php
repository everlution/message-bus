<?php

declare(strict_types=1);

require __DIR__ . '/bootstrap.php';

try {
    $simulator->simulate($pingMessage);
} catch (\Everlution\MessageBus\Validator\ValidatorException $e) {
    var_dump($e->getJsonSchema());
    var_dump($e->getData());
    var_dump($e->getErrors());
}

