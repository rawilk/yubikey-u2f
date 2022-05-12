<?php

namespace Rawilk\Yubikey\Exceptions;

use Exception;

class YubikeyIdentityException extends Exception
{
    public static function alreadyBound(): self
    {
        return new static(__('yubikey-u2f::alerts.already_bound'));
    }

    public static function maxReached(): self
    {
        return new static(__('yubikey-u2f::alerts.max_identities_reached'));
    }
}
