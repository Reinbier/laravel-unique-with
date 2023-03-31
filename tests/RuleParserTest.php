<?php

it('is initializable', function () {
    expect(getRuleParser())->toBeObject();
});

it('can parse the table name correctly', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name']))
        ->getTable()
        ->toBe('users');
});

it('can parse the primary field correctly', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name']))
        ->getPrimaryField()
        ->toBe('first_name');
});

it('can parse the primary value correctly', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name']))
        ->getPrimaryValue()
        ->toBe('Foo');
});

it('can parse one additional field correctly', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name'], ['last_name' => 'Bar']))
        ->getAdditionalFields()
        ->toBeArray()
        ->toHaveKey('last_name', 'Bar');
});

it('can parse two additional fields correctly', function () {
    expect(getRuleParser(
            'first_name',
            'Foo',
            ['users', 'middle_name', 'last_name'],
            ['middle_name' => 'Quux', 'last_name' => 'Bar'])
    )
        ->getAdditionalFields()
        ->toBeArray()
        ->toHaveKey('middle_name', 'Quux')
        ->toHaveKey('last_name', 'Bar');
});

it('can parse custom name for primary field correctly', function () {
    expect(getRuleParser(
            'first_name',
            'Foo',
            ['users', 'first_name = firstName', 'last_name'],
            ['last_name' => 'Bar'])
    )
        ->getPrimaryField()
        ->toBe('firstName');
});

it('can parse custom name for additional field correctly', function () {
    expect(getRuleParser(
            'first_name',
            'Foo',
            ['users', 'last_name = sur_name'],
            ['last_name' => 'Bar'])
    )
        ->getAdditionalFields()
        ->toHaveKey('sur_name', 'Bar');
});

it('has no ignore value by default', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name']))
        ->getIgnoreValue()
        ->toBe(null);
});

it('has no ignore column by default', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name']))
        ->getIgnoreColumn()
        ->toBe(null);
});

it('can parse implicit integer ignore value correctly', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name', '1']))
        ->getIgnoreValue()
        ->toBe('1');
});

it('can parse implicit integer ignore column correctly', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name', '1 = user_id']))
        ->getIgnoreColumn()
        ->toBe('user_id');
});

it('can parse explicit ignore value correctly', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name', 'ignore:abc123']))
        ->getIgnoreValue()
        ->toBe('abc123');
});

it('can parse explicit ignore column correctly', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name', 'ignore:abc123 = user_id']))
        ->getIgnoreColumn()
        ->toBe('user_id');
});

it('can parse dot notation for object correctly', function () {
    expect(getRuleParser(
            'name.first',
            'Foo',
            ['users', 'name.first = first_name', 'name.last = last_name'],
            [
                'name' => [
                    'first' => 'Foo',
                    'last' => 'Bar',
                ],
            ]
        )
    )
        ->getPrimaryField()->toBe('first_name')
        ->getAdditionalFields()->toHaveKey('last_name', 'Bar');
});

it('can parse dot notation for first entry of array correctly', function () {
    expect(getRuleParser(
            'users.0.first',
            'Foo',
            ['users', 'users.*.first = first_name', 'users.*.last = last_name'],
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
        )
    )
        ->getPrimaryField()->toBe('first_name')
        ->getAdditionalFields()->toHaveKey('last_name', 'Bar');
});

it('can parse dot notation for second entry of array correctly', function () {
    expect(getRuleParser('users.1.first', 'Baz', ['users', 'users.*.first = first_name', 'users.*.last = last_name'], [
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
    ]))
        ->getPrimaryField()->toBe('first_name')
        ->getAdditionalFields()->toHaveKey('last_name', 'Quux');
});

it('can parse dot notation for top level array correctly', function () {
    expect(getRuleParser('1.first_name', 'Baz', ['users', '*.first_name = first_name', '*.last_name = last_name'], [
        [
            'first_name' => 'Foo',
            'last_name' => 'Bar',
        ],
        [
            'first_name' => 'Baz',
            'last_name' => 'Quux',
        ],
    ]))
        ->getPrimaryField()->toBe('first_name')
        ->getAdditionalFields()->toHaveKey('last_name', 'Quux');
});

it('returns data fields correctly', function () {
    expect(getRuleParser(
        'first_name',
        'Foo',
        ['users', 'first_name = firstName', 'middle_name', 'last_name = lastName', 'ignore:abc123 = user_id']
    ))
        ->getDataFields()
        ->toBeArray()
        ->toBe(['first_name', 'middle_name', 'last_name']);
});

it('returns null for connection if not specified', function () {
    expect(getRuleParser('first_name', 'Foo', ['users', 'last_name']))
        ->getConnection()
        ->toBe(null);
});

it('can parse custom connection if specified', function () {
    expect(getRuleParser('first_name', 'Foo', ['other-connection.users', 'last_name']))
        ->getConnection()->toBe('other-connection')
        ->getTable()->toBe('users');
});