<?php

namespace Reinbier\LaravelUniqueWith;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Reinbier\LaravelUniqueWith\Commands\LaravelUniqueWithCommand;

class LaravelUniqueWithServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-unique-with')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-unique-with_table')
            ->hasCommand(LaravelUniqueWithCommand::class);
    }
}
