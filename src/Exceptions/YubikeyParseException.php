<?php

namespace Rawilk\Yubikey\Exceptions;

use Exception;

class YubikeyParseException extends Exception
{
    public static function invalidToken(): self
    {
        return new static('The system could not parse the provided YubiKey OTP token.');
    }
}
