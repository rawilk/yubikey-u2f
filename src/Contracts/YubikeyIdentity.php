<?php

namespace Rawilk\Yubikey\Contracts;

interface YubikeyIdentity
{
    public static function associate(string $identity, $userId, string $keyName = 'Security key'): self;

    public static function verify(string $otp, $userId): bool;

    public function isOwnedBy($user): bool;
}
