<?php

namespace Rawilk\Yubikey\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Rawilk\Yubikey\Policies\YubikeyIdentityPolicy;

class YubikeyAuthProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->policies[config('yubikey-u2f.database.model')] = YubikeyIdentityPolicy::class;

        $this->registerPolicies();
    }
}
