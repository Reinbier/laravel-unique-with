<?php

namespace Reinbier\LaravelUniqueWith\Tests\models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $connection = 'testing';

    protected $table = 'users';

    protected $fillable = ['first_name', 'middle_name', 'last_name'];
}
