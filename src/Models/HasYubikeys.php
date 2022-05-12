<?php

namespace Rawilk\Yubikey\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Rawilk\Yubikey\Contracts\YubikeyIdentity as YubikeyIdentityContract;

/**
 * @mixin \Illuminate\Database\Eloquent\Model
 */
trait HasYubikeys
{
    public function yubikeys(): HasMany
    {
        return $this->hasMany(config('yubikey-u2f.database.model'));
    }

    public function associateYubikeyIdentity(string $identity, string $keyName = 'Security key'): YubikeyIdentityContract
    {
        return app(config('yubikey-u2f.database.model'))::associate(
            $identity,
            $this->getKey(),
            $keyName,
        );
    }

    public function verifyYubikeyIdentity(string $otp): bool
    {
        return app(config('yubikey-u2f.database.model'))::verify(
            $otp,
            $this->getKey(),
        );
    }
}
