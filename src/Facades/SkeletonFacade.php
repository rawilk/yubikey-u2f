<?php

namespace Rawilk\Yubikey\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rawilk\Yubikey\Yubikey
 */
class YubikeyFacade extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'yubikey-u2f';
    }
}
