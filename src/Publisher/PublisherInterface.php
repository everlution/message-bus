<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Publisher;

use Everlution\MessageBus\Validator\ValidatorException;

interface PublisherInterface
{
    /**
     * @param array $data
     * @throws ValidatorException
     */
    public function publish(array $data): void;
}
