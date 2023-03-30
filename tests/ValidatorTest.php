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

function validateData(array $rules = [], array $data = []): Illuminate\Validation\Validator
{
    return Validator::make($data, $rules);
}

it('passes validation if there are no existing database rows', function () {
    User::truncate();

    expect(validateData(
        ['first_name' => 'unique_with:users,last_name'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ]
    )->passes())->toBeTrue();
});

it('fails validation if there are existing database rows', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,last_name'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ]
    )->passes())->toBeFalse();
});

it('reads parameters without explicit column names', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,middle_name,last_name'],
        [
            'first_name' => 'Foo',
            'middle_name' => 'Bar',
            'last_name' => 'Baz',
        ]
    )->passes())->toBeTrue();
});

it('reads parameters with explicit column names', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,tussenvoegsel=middle_name,achternaam=last_name'],
        [
            'first_name' => 'Alexander',
            'tussenvoegsel' => 'Graham',
            'achternaam' => 'Bell',
        ]
    )->passes())->toBeFalse();
});

it('reads implicit integer ignore id with default column name', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,last_name,1'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ]
    )->passes())->toBeTrue();
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
    )->passes())->toBeFalse()
        ->and(validateData(
            ['name' => 'unique_with:dogs,owner,1 = tag'],
            [
                'name' => 'Poodle',
                'owner' => 'A. Bell',
            ]
        )->passes())->ToBeTrue();
});

it('reads explicit ignore id with default column name', function () {
    expect(validateData(
        ['first_name' => 'unique_with:users,last_name,ignore:1'],
        [
            'first_name' => 'Alexander',
            'last_name' => 'Bell',
        ])->passes())->toBeTrue();
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
    )->passes())->ToBeTrue();
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
        ])->passes())->toBeTrue();
});

//it('replaces_fields_in_error_message_correctly', function () {
//    $validator = validateData(
//        ['first_name' => 'unique_with:users,last_name'],
//        [
//            'first_name' => 'Alexander',
//            'last_name' => 'Bell',
//        ]
//    );
//
//    $expectedErrorMessage = str_replace(':fields', 'first name, last name', 'This combination of :fields already exists.');
//    expect($validator->getMessageBag()->toArray())->toBe(['first_name' => [$expectedErrorMessage]]);
//});

//function it_uses_custom_error_message_coming_from_translator()
//{
//    $customErrorMessage = 'Error: Found combination of :fields in database.';
//
//    $this->presenceVerifier->getCount(Argument::cetera())->willReturn(1);
//    $this->trans('uniquewith-validator::validation.unique_with')->shouldBeCalled()
//        ->willReturn($customErrorMessage);
//    $this->trans('validation.attributes')->shouldBeCalled()->willReturn([]);
//
//    $this->validateData(
//        ['first_name' => 'unique_with:users,first_name,middle_name,last_name'],
//        [
//            'first_name' => 'Foo',
//            'middle_name' => 'Bar',
//            'last_name' => 'Baz',
//        ]
//    );
//
//    $expectedErrorMessage = str_replace(':fields', 'first name, middle name, last name', $customErrorMessage);
//    expect($this->validator->getMessageBag()->toArray())->toBe(['first_name' => [$expectedErrorMessage]]);
//}
//
//function it_uses_custom_attribute_names_coming_from_translator()
//{
//    $this->presenceVerifier->getCount(Argument::cetera())->willReturn(1);
//
//    $this->trans('validation.attributes')->shouldBeCalled()->willReturn([
//        'first_name' => 'Vorname',
//        'last_name' => 'Nachname',
//    ]);
//
//    $this->validateData(
//        ['first_name' => 'unique_with:users,first_name,last_name'],
//        [
//            'first_name' => 'Foo',
//            'last_name' => 'Bar',
//        ]
//    );
//
//    $expectedErrorMessage = str_replace(':fields', 'Vorname, Nachname', $this->getValidationMessage());
//    expect($this->validator->getMessageBag()->toArray())->toBe(['first_name' => [$expectedErrorMessage]]);
//}
//
//function it_supports_dot_notation_for_an_object_in_rules()
//{
//    $this->validateData(
//        ['name.first' => 'unique_with:users, name.first = first_name, name.last = last_name'],
//        [
//            'name' => [
//                'first' => 'Foo',
//                'last' => 'Bar',
//            ],
//        ]
//    );
//
//    $this->presenceVerifier->getCount(
//        'users',
//        'first_name',
//        'Foo',
//        null,
//        null,
//        ['last_name' => 'Bar']
//    )->shouldHaveBeenCalled();
//}
//
//function it_supports_dot_notation_for_an_array_in_rules()
//{
//    $this->validateData(
//        ['users.*.first' => 'unique_with:users, users.*.first = first_name, users.*.last = last_name'],
//        [
//            'users' => [
//                [
//                    'first' => 'Foo',
//                    'last' => 'Bar',
//                ],
//                [
//                    'first' => 'Baz',
//                    'last' => 'Quux',
//                ],
//            ],
//        ]
//    );
//
//    $this->presenceVerifier->getCount(
//        'users',
//        'first_name',
//        'Foo',
//        null,
//        null,
//        ['last_name' => 'Bar']
//    )->shouldHaveBeenCalled();
//
//    $this->presenceVerifier->getCount(
//        'users',
//        'first_name',
//        'Baz',
//        null,
//        null,
//        ['last_name' => 'Quux']
//    )->shouldHaveBeenCalled();
//}
//
//function it_uses_connection_if_specified(DatabasePresenceVerifier $dbVerifier)
//{
//    $this->presenceVerifier = $dbVerifier;
//
//    $this->validateData(
//        ['first_name' => 'unique_with:db.users,middle_name,last_name'],
//        [
//            'first_name' => 'Foo',
//            'middle_name' => 'Bar',
//            'last_name' => 'Baz',
//        ]
//    );
//
//    $this->presenceVerifier->setConnection('db')->shouldHaveBeenCalled();
//    $this->presenceVerifier->getCount(
//        'users',
//        'first_name',
//        'Foo',
//        null,
//        null,
//        ['middle_name' => 'Bar', 'last_name' => 'Baz']
//    )->shouldHaveBeenCalled();
//}
//
