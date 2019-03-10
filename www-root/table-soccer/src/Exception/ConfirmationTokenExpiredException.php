<?php

namespace App\Exception;

class ConfirmationTokenExpiredException extends \RuntimeException
{
    /**
     * @param string|null $message
     *
     * @return ConfirmationTokenExpiredException
     */
    public static function create(?string $message = 'Given confirmation token is already expired.'): self
    {
        return (new self($message));
    }
}
