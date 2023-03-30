<?php

namespace Reinbier\LaravelUniqueWith\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Reinbier\LaravelUniqueWith\LaravelUniqueWith
 */
class LaravelUniqueWith extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Reinbier\LaravelUniqueWith\LaravelUniqueWith::class;
    }
}
