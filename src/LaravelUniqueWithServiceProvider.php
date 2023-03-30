<?php

namespace Reinbier\LaravelUniqueWith;

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
        $message = app('translator')->get('laravel-unique-with::validation.unique_with');

        Validator::extend('unique_with', LaravelUniqueWith::class.'@validateUniqueWith', $message);
        Validator::replacer('unique_with', function () {
            // Since 5.4.20, the validator is passed in as the 5th parameter.
            // In order to preserve backwards compatibility, we check if the
            // validator is passed and use the validator's translator instead
            // of getting it out of the container.
            $arguments = func_get_args();
            if (count($arguments) >= 5) {
                $arguments[4] = $arguments[4]->getTranslator();
            } else {
                $arguments[4] = app('translator');
            }

            return call_user_func_array([new LaravelUniqueWith, 'replaceUniqueWith'], $arguments);
        });
    }
}
