# Unique With Validator rule for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/reinbier/laravel-unique-with.svg?style=flat-square)](https://packagist.org/packages/reinbier/laravel-unique-with)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/reinbier/laravel-unique-with/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/reinbier/laravel-unique-with/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/reinbier/laravel-unique-with/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/reinbier/laravel-unique-with/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/reinbier/laravel-unique-with.svg?style=flat-square)](https://packagist.org/packages/reinbier/laravel-unique-with)

This package contains a variant of the validateUnique rule for Laravel, that allows for validation of multi-column UNIQUE indexes.

#### Please note
_This package is to continue development of the package [uniquewith-validator](https://github.com/felixkiss/uniquewith-validator) by [Felix Kiss](https://github.com/felixkiss) and have it work with recent versions of the Laravel framework (continuing from Laravel 9 onwards). For older versions of the framework, please use the aforementioned package._

## Installation

You can install the package via composer:

```bash
composer require reinbier/laravel-unique-with
```

## Usage

Use it like any `Validator` rule:

```php
$rules = [
    '<field1>' => 'unique_with:<table>,<field2>[,<field3>,...,<ignore_rowid>]',
];
```

See the [Validation documentation](http://laravel.com/docs/validation) of Laravel.

### Specify different column names in the database

If your input field names are different from the corresponding database columns,
you can specify the column names explicitly.

e.g. your input contains a field 'last_name', but the column in your database is called 'sur_name':
```php
$rules = [
    'first_name' => 'unique_with:users, middle_name, last_name = sur_name',
];
```

### Ignore existing row (useful when updating)

You can also specify a row id to ignore (useful to solve unique constraint when updating)

This will ignore row with id 2

```php
$rules = [
    'first_name' => 'required|unique_with:users,last_name,2',
    'last_name' => 'required',
];
```

To specify a custom column name for the id, pass it like

```php
$rules = [
    'first_name' => 'required|unique_with:users,last_name,2 = custom_id_column',
    'last_name' => 'required',
];
```

If your id is not numeric, you can tell the validator

```php
$rules = [
    'first_name' => 'required|unique_with:users,last_name,ignore:abc123',
    'last_name' => 'required',
];
```

### Add additional clauses (e.g. when using soft deletes)

You can also set additional clauses. For example, if your model uses soft deleting
then you can use the following code to select all existing rows but marked as deleted

```php
$rules = [
    'first_name' => 'required|unique_with:users,last_name,deleted_at,2 = custom_id_column',
    'last_name' => 'required',
];
```

*Soft delete caveat:*

If the validation is performed in a form request class, field deleted_at is skipped, because it's not send in request. 
To solve this problem, add 'deleted_at' => null to your validation parameters in request class., e.g.:

```php
protected function validationData()
{
    return array_merge($this->request->all(), [
        'deleted_at' => null
    ]);
}
```

### Specify specific database connection to use

If we have a connection named `some-database`, we can enforce this connection (rather than the default) like this:

```php
$rules = [
    'first_name' => 'unique_with:some-database.users, middle_name, last_name',
];
```

## Example

Pretend you have a `users` table in your database plus `User` model like this:

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

return new class extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function(Blueprint $table) {
            $table->increments('id');

            $table->timestamps();

            $table->string('first_name');
            $table->string('last_name');

            $table->unique(['first_name', 'last_name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('users');
    }

}
```

```php
<?php

class User extends Model { }
```

Now you can validate a given `first_name`, `last_name` combination with something like this:

```php
Route::post('test', function() {
    $rules = [
        'first_name' => 'required|unique_with:users,last_name',
        'last_name' => 'required',
    ];

    $validator = Validator::make(Input::all(), $rules);

    if($validator->fails()) {
        return Redirect::back()->withErrors($validator);
    }

    $user = new User;
    $user->first_name = Input::get('first_name');
    $user->last_name = Input::get('last_name');
    $user->save();

    return Redirect::home()->with('success', 'User created!');
});
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [reinbier](https://github.com/Reinbier)
- [All Contributors](../../contributors)

The code from this package is based on the original code that came from [uniquewith-validator](https://github.com/felixkiss/uniquewith-validator) by [Felix Kiss](https://github.com/felixkiss).

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
