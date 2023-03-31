<?php

use Reinbier\LaravelUniqueWith\Tests\models\Dog;
use Reinbier\LaravelUniqueWith\Tests\models\Tree;
use Reinbier\LaravelUniqueWith\Tests\models\User;

beforeEach(function () {
    User::create([
        'first_name' => 'Alexander',
        'middle_name' => 'Graham',
        'last_name' => 'Bell',
    ]);
});

it('passes validation if there are no existing database rows', function () {
    User::truncate();

    expect(validateData(
        ['first_name' => 'unique_with:users,last_name'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ]
    ))->passes()->toBeTrue();
});

it('fails validation if there are existing database rows', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,last_name'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ]
    ))->passes()->toBeFalse();
});

it('reads parameters without explicit column names', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,middle_name,last_name'],
        [
            'first_name' => 'Foo',
            'middle_name' => 'Bar',
            'last_name' => 'Baz',
        ]
    ))->passes()->toBeTrue();
});

it('reads parameters with explicit column names', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,tussenvoegsel=middle_name,achternaam=last_name'],
        [
            'first_name' => 'Alexander',
            'tussenvoegsel' => 'Graham',
            'achternaam' => 'Bell',
        ]
    ))->passes()->toBeFalse();
});

it('reads implicit integer ignore id with default column name', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,last_name,1'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ]
    ))->passes()->toBeTrue();
});

it('reads implicit integer ignore id with custom column name', function () {
    Dog::create([
        'name' => 'Poodle',
        'owner' => 'A. Bell',
    ]);

    expect(validateData(
        ['name' => 'unique_with:dogs,owner,1'],
        [
            'name' => 'Poodle',
            'owner' => 'A. Bell',
        ]
    ))->passes()->toBeFalse()
        ->and(validateData(
            ['name' => 'unique_with:dogs,owner,1 = tag'],
            [
                'name' => 'Poodle',
                'owner' => 'A. Bell',
            ]
        ))->passes()->ToBeTrue();
});

it('reads explicit ignore id with default column name', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,last_name,ignore:1'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ]))->passes()->toBeTrue();
});

it('reads explicit ignore id with custom column name', function () {
    Dog::create([
        'name' => 'Poodle',
        'owner' => 'A. Bell',
    ]);

    expect(validateData(
        ['name' => 'unique_with:dogs,owner,ignore:1 = tag'],
        [
            'name' => 'Poodle',
            'owner' => 'A. Bell',
        ]
    ))->passes()->ToBeTrue();
});

it('reads explicit ignore non-numeric id with default column name', function () {
    Tree::create([
        'location' => 'Canada',
        'name' => 'Pine',
        'color' => 'Green',
    ]);

    expect(validateData(
        ['location' => 'unique_with:trees,name,color,ignore:Canada = location'],
        [
            'location' => 'Canada',
            'name' => 'Pine',
            'color' => 'Green',
        ]))->passes()->toBeTrue();
});

it('replaces_fields_in_error_message_correctly', function () {
    $validator = validateData(
        ['first_name' => 'unique_with:users,last_name'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ]
    );

    $expectedErrorMessage = str_replace(':fields', 'first name, last name', getValidationMessage());
    expect($validator->getMessageBag()->toArray())->toBe(['first_name' => [$expectedErrorMessage]]);
});

it('uses custom attribute names coming from validator', function () {
    $validator = validateData(
        ['first_name' => 'unique_with:users,last_name'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ], [
            'first_name' => 'Voornaam',
            'last_name' => 'Achternaam',
        ]
    );

    $expectedErrorMessage = str_replace(':fields', 'Voornaam, Achternaam', getValidationMessage());
    expect($validator->getMessageBag()->toArray())->toBe(['first_name' => [$expectedErrorMessage]]);
});

it('supports_dot_notation_for_an_object_in_rules', function () {
    expect(validateData(
        ['name.first' => 'unique_with:users, name.first = first_name, name.last = last_name'],
        [
            'name' => [
                'first' => 'Alexander',
                'last' => 'Bell',
            ],
        ]
    ))->passes()->toBeFalse();
});

it('supports_dot_notation_for_an_array_in_rules', function () {
    expect(validateData(
        ['users.*.first' => 'unique_with:users, users.*.first = first_name, users.*.last = last_name'],
        [
            'users' => [
                [
                    'first' => 'Foo',
                    'last' => 'Bar',
                ],
                [
                    'first' => 'Baz',
                    'last' => 'Quux',
                ],
            ],
        ]
    ))->getMessageBag()->toBeEmpty();
});
