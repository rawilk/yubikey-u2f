<?php

namespace Rawilk\Yubikey\Tests;

use Dotenv\Dotenv;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;
use Rawilk\Yubikey\YubikeyServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        $this->loadEnvironmentVariables();

        parent::setUp();
    }

    protected function getPackageProviders($app): array
    {
        return [
            YubikeyServiceProvider::class,
        ];
    }

    protected function loadEnvironmentVariables(): void
    {
        if (! file_exists(__DIR__ . '/../.env')) {
            return;
        }

        $dotEnv = Dotenv::createImmutable(__DIR__ . '/..');

        $dotEnv->load();
    }

    public function getEnvironmentSetUp($app)
    {
        $testMigrations = [
            'create_users_table.php',
        ];
        foreach ($testMigrations as $path) {
            $migration = include __DIR__ . '/database/migrations/' . $path;
            $migration->up();
        }

        $migration = include __DIR__ . '/../database/migrations/create_yubikey_u2f_table.php.stub';
        $migration->up();
    }
}
