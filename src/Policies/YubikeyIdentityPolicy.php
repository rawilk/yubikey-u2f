<?php

namespace Rawilk\Yubikey\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Rawilk\Yubikey\Contracts\YubikeyIdentity;

class YubikeyIdentityPolicy
{
    use HandlesAuthorization;

    public function delete($user, YubikeyIdentity $yubikeyIdentity): bool
    {
        return $yubikeyIdentity->isOwnedBy($user);
    }

    public function rename($user, YubikeyIdentity $yubikeyIdentity): bool
    {
        return $yubikeyIdentity->isOwnedBy($user);
    }
}
