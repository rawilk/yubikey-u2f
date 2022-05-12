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

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Rawilk\\Yubikey\\Database\\Factories\\' . class_basename($modelName) . 'Factory'
        );
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
        // include_once __DIR__ . '/../database/migrations/create_yubikey-u2f_table.php.stub';
        // (new \CreatePackageTable())->up();
    }
}
