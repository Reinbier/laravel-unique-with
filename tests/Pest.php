<?php

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Reinbier\LaravelUniqueWith\Tests\TestCase;

uses(TestCase::class, LazilyRefreshDatabase::class)->in(__DIR__);

function validateData(
    array $rules = [],
    array $data = [],
    array $customAttributes = []
): Illuminate\Validation\Validator {
    return Validator::make($data, $rules, [], $customAttributes);
}

function getValidationMessage(): string
{
    return __('unique-with::validation.unique_with');
}
