<?php

namespace App\Services\Auth\Exceptions;

use Exception;

class VerificationOtpDeliveryException extends Exception
{
    public function __construct(
        public readonly string $reason,
        string $message = '',
        public readonly array $context = [],
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message !== '' ? $message : $reason, $code, $previous);
    }
}
