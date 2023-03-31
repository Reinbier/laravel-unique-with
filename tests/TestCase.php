<?php

namespace Reinbier\LaravelUniqueWith\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Reinbier\LaravelUniqueWith\LaravelUniqueWithServiceProvider;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function getPackageProviders($app)
    {
        return [
            LaravelUniqueWithServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('app.locale', 'en');
        config()->set('database.default', 'testing');
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        config()->set('database.connections.other-connection', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);

        $migration = include __DIR__.'/migrations/create_laravel-unique-with_tables.php.stub';
        $migration->up();
    }
}
