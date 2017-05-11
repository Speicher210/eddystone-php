<?php

declare(strict_types = 1);

namespace Wingu\Eddystone\Exception;

final class InvalidHttpUrlException extends \InvalidArgumentException
{
    public static function reason(string $msg): self
    {
        return new self('Invalid HTTP URL because ' . $msg);
    }
}
