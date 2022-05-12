<?php

declare(strict_types=1);

namespace Rawilk\Yubikey\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Rawilk\Yubikey\Yubikey
 *
 * @method static string getOtpIdentity(string $otp)
 * @method static \Rawilk\Yubikey\Yubikey useTimestamp()
 * @method static \Rawilk\Yubikey\Yubikey sl(string $sl)
 * @method static \Rawilk\Yubikey\Yubikey timeout(int $timeout)
 * @method static array verify(string $token)
 */
class Yubikey extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Rawilk\Yubikey\Yubikey::class;
    }
}
