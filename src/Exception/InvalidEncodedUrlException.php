<?php

declare(strict_types = 1);

namespace Wingu\Eddystone\Exception;

final class InvalidEncodedUrlException extends \InvalidArgumentException
{
    public static function reason(string $msg): self
    {
        return new self('Invalid eddystone encoded url because ' . $msg);
    }
}
