<?php

namespace Rawilk\Yubikey;

use Rawilk\Yubikey\Providers\YubikeyAuthProvider;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class YubikeyServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('yubikey-u2f')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_yubikey_u2f_table')
            ->hasTranslations();
    }

    public function packageRegistered(): void
    {
        $this->app->register(YubikeyAuthProvider::class);

        $this->app->singleton(
            Yubikey::class,
            fn ($app) => new Yubikey(
                $app['config']['yubikey-u2f.client_id'],
                $app['config']['yubikey-u2f.secret_key']
            )
        );
    }

    public function provides(): array
    {
        return [
            Yubikey::class,
        ];
    }
}
