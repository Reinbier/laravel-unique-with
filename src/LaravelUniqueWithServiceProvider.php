<?php

namespace Reinbier\LaravelUniqueWith;

use Illuminate\Support\Facades\Validator;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

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
            ->hasTranslations();
    }

    public function packageBooted()
    {
        $message = app('translator')->get('unique-with::validation.unique_with');

        Validator::extend('unique_with', LaravelUniqueWith::class.'@validateUniqueWith', $message);
        Validator::replacer('unique_with', function () {
            return call_user_func_array([new LaravelUniqueWith, 'replaceUniqueWith'], func_get_args());
        });
    }
}
