<?php

declare(strict_types=1);

namespace Everlution\MessageBus\Validator;

use Everlution\MessageBus\Protocol\ProtocolInterface;

interface ValidatorInterface
{
    /**
     * @param ProtocolInterface $protocol
     * @param array $message
     * @throws ValidatorException
     */
    public function validate(ProtocolInterface $protocol, array $message): void;
}
