<?php

namespace Reinbier\LaravelUniqueWith\Tests\models;

use Illuminate\Database\Eloquent\Model;

class Tree extends Model
{
    protected $connection = 'testing';

    protected $table = 'trees';

    protected $primaryKey = 'location';

    public $incrementing = false;

    protected $fillable = ['location', 'name', 'color'];
}
