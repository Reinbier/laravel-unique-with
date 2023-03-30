<?php

namespace Reinbier\LaravelUniqueWith\Tests\models;

use Illuminate\Database\Eloquent\Model;

class Dog extends Model
{
    protected $connection = 'testing';

    protected $table = 'dogs';

    protected $primaryKey = 'tag';

    protected $fillable = ['name', 'owner'];
}
