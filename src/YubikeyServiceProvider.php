<?php

namespace Rawilk\Yubikey;

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
            ->hasMigration('create_yubikey-u2f_table');
    }
}
