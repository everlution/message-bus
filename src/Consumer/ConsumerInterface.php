<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Consumer;

use Everlution\MessageBus\Validator\ValidatorException;

interface ConsumerInterface
{
    /**
     * @param int $numberOfMessages
     * @throws ValidatorException
     */
    public function consume(int $numberOfMessages): void;
}
