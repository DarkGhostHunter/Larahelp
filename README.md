![Regine Tholen - Unsplash (UL) #ojGvj7CE5OQ](https://images.unsplash.com/photo-1574246915327-8cf501d94757?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/larahelp/v/stable)](https://packagist.org/packages/darkghosthunter/larahelp) [![License](https://poser.pugx.org/darkghosthunter/larahelp/license)](https://packagist.org/packages/darkghosthunter/larahelp)
![](https://img.shields.io/packagist/php-v/darkghosthunter/larahelp.svg)
 ![](https://github.com/DarkGhostHunter/Larahelp/workflows/PHP%20Composer/badge.svg)
 [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Larahelp/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Larahelp?branch=master)


# Larahelp

Supercharge your Laravel projects with more than 25 useful global helpers.

## Requisites

* Laravel 8.x or later
* PHP 8.0 or later

## Installation

You can install the package via composer:

```bash
composer require darkghosthunter/larahelp
```

> This package is focused on the backend. If you want views helpers, I recommend you to use [custom Blade directives](https://laravel.com/docs/blade#extending-blade) instead.

## Usage

This package includes helpful global helpers for your project make almost anything into beautiful _one liners_:

| | | |
|---|---|---|
| [app_call](#app_call)             | [in_development](#in_development) | [route_is](#route_is)
| [call_existing](#call_existing)   | [logged_in](#logged_in)           | [shadow](#shadow)
| [created](#created)               | [methods_of](#methods_of)         | [sleep_between](#sleep_between)
| [data_update](#data_update)       | [missing_trait](#missing_trait)   | [taptap](#taptap)
| [delist](#delist)                 | [none_of](#none_of)               | [undot_path](#undot_path)
| [diff](#diff)                     | [object_assign](#object_assign)   | [until](#until)
| [dot_path](#dot_path)             | [ok](#ok)                         | [user](#user)
| [enclose](#enclose)               | [period](#period)                 | [weekend](#weekend)
| [files](#files)                   | [period_from](#period_from)       | [weekstart](#weekstart)
| [has_trait](#has_trait)           | [pipe](#pipe)                     | [which_of](#which_of)
| [hashy](#hashy)                   | [remember](#remember)             | [yesterday](#yesterday)
| [in_console](#in_console)         | [route_is](#route_is)             |
### `app_call()`

Executes a callable using the application Container.

```php
$result = app_call('\App\MyService\Service@createSomething');
```

### `call_existing()`

Calls a dynamic method or macro if it exists in the object instance. It supports macros and static methods.

```php
class Something {
    public function foo() {
        return 'bar';
    }
}

call_existing(new Something, 'bar'); // null

call_existing(new Something, 'foo'); // "bar"
```

### `created()`

Return an HTTP 201 response (OK, Created), with optional content.

```php
public function store(Request $request): Response
{
    // ...
    
    return created([$post->getKeyName() => $post->getKey()];
}
```

### `data_update()`

Updates an item of an array or object using a callback that receives it.

```php
$array = [
    'foo' => [
        'bar' => null
    ]
];

data_update($array, 'foo.bar', function ($value) {
    if ($value === null) {
        return 'baz';
    }
})


// [
//     'foo' => [
//         'bar' => 'baz'
//     ]
// ];
```

> The value will be updated regardless if the key doesn't exists.

### `delist()`

Returns the values of the array, so these can be listed into variables. It accepts an optional offset to remove a given number of first keys.

```php
$array = [
    'foo'  => 'bar',
    'baz'  => 'qux',
    'quux' => 'quuz',
]

[$baz, $quux] = delist($array, 1);
```

### `diff()`

Returns the difference between two dates in seconds or any other given unit.

```php
$seconds = diff('now', '15th may');

$minutes = diff('today', now())
```

### `dot_path()`

Transforms a relative path into dot notation.

```php
$path = dot_path('files/user_id_312/videos/');

// files.user_id_312.videos
```

> This does not validate file paths. You should append the filename to the dot-path manually. 

### `enclose()`

Wraps a value or callable into a Closure, if it's not already callable.

```php
$enclosed = enclose('foo');

$enclosed();

// "foo"
```

### `files()`

Returns the local Filesystem helper, or a list of files in a path.

```php
$content = files()->get('text.txt');
```

### `has_trait()`

Checks recursively if the object is using a single trait.

```php
trait Child {
    // ..
}

trait Parent {
    use Child; 
    // ..
}

class Foo {
    use Parent;
}

has_trait(Foo::class, Child::class);

// true
```

### `hashy()`

Creates a small BASE64 encoded MD5 hash from a string for portable checksum.

This is very useful to hash large walls of texts, or even files, while compressing the 128-bit hash into a 24-character string.

```php
$hash = hashy('This is a hashable string');

// "TJYa8+63dRbdN6w44shX1g=="
```

You can use the same function to compare the hashable string with a hash to note if it was modified.

```php
hashy('This is a hashable string', 'TJYa8+63dRbdN6w44shX1g==');

// true

hashy('This is a hashable string!', 'TJYa8+63dRbdN6w44shX1g==');

// false
```

### `in_console()`

Check if the application is running in console, like when using Artisan or PHPUnit.

```php
if (in_console()) {
    return "We're in console";
}
```

It also accepts a callable that will be executed if the condition is true.

```php
$result = in_console(fn() => 'foo');

// "foo"
```

### `in_development()`

Check if the application is running in development environments: `dev`, `development` or `local`

```php
if (in_development()) {
    return "Do anything, it doesn't matter!";
}
```

It also accepts a callable that will be executed if the condition is true.

```php
$result = in_development(fn() => 'foo');

// "foo"
```

### `logged_in()`

Executes a single callback while the user is logged in.

It basically logs in and logs out an user while executing the callback, which can be useful to do on guest routes.

```php
use App\Models\User;

$user = User::find(1);

$post = logged_in($user, function (User $user) {
    return $user->post()->create([
        // ..
    ]);
});
```

> It will throw an exception if there is already a user logged in.

### `methods_of()`

Returns a collection of all public methods from a given class or object.

```php
use Illuminate\Support\Collection;

$methods = methods_of(Collection::class)

return $methods->has('make');

// true
```

### `missing_trait()`

Checks recursively if the object is not using a trait.

```php
trait Parent {
    // ..
}

class Foo {
    use Parent;
}

missing_trait(Foo::class, Child::class);

// true
```

### `none_of()`

Checks if none of the options compared to a subject, or called with it, returns something truthy.

```php
$subject = 'foo';

none_of('foo', ['bar', 'baz', 'qux']);

// false
```

Using a callable, it will receive the subject and the key being compared.

```php
$subject = 'foo';

none_of('foo', ['bar', 'baz', 'qux'], fn ($subject, $compared) => $subject === $compared);

// false
```

### `object_assign()`

Assigns an array of values to an object, recursively, using dot notation.

```php
$object = new stdClass();

object_assign($object, ['foo' => 'bar']);

echo $object->foo; // "bar"
```

### `ok()`

Returns an HTTP 204 response (OK, No Content).

```php
public function send(Request $request)
{
    // ...
    
    return ok();
}
```

### `period()`

Returns the period of a given start and end or interval. Periods are extremely useful to get multiple dates based on a start, end, a given time between each occurrence.

The most common way to create a period is defining the start, the number of occurrences, and the interval amount.

```php
$periods = period('now', 4, '15 minutes');
```

Alternatively, you can specify a start, and end, and the interval of time until that end.

```php
$period = period('today', 'last day of this month', '3 days');
```

Once you do, you can iterate each moment of time with a `foreach` loop.

```php
foreach (period('today', 'last day of this month', '3 days') as $datetime) {
    echo $datetime->toDateTimeString();
}

// 2015-07-21 00:00:00
// 2015-07-24 00:00:00
// 2015-07-27 00:00:00
// 2015-07-30 00:00:00
```

### `period_from()`

Returns the next or previous period from given date, exclusive by default.

This is very handy to know when the next or previous moment from a given datetime, like the current time.

```php
$period = period('2015-07-06 00:00:00', '2015-07-26 00:00:00', '1 week');

period_from($period, '2015-07-13');

// 2015-07-13
```

The function can also check the previous date by setting `$after` to `false`.

```php
$period = period('2015-07-06 00:00:00', '2015-07-26 00:00:00', '1 week');

period_from($period, '2015-07-06');

// 2015-07-13
```

> Dates compared are exclusive. In include the date as inclusive, set `$inclusive` to `true`.

### `pipe()`

Sends an object through a pipeline.

```php
pipe(10, [
    fn($integer, $next) => $next($integer + 10);
    fn($integer, $next) => $next($integer - 5);
])

// 15
```

### `remember()`

Retrieves an item from the cache, or stores a default value if the item doesn't exist. 

```php
remember('foo', 60, function() {
    return 'bar';
})
```

If no `ttl` is set, and rather a callback is issued as second parameter, it will be stored forever.

```php
remember('foo', function () {
    return 'bar';
})
```

It supports atomic locks, which are created using the same name key. It will lock the key by a given seconds, while also waiting for the same amount of time.

```php
remember('foo', 60, function() {
    return 'bar';
}, 20);
```

This can be useful to avoid cache data-races, where multiple processes run the same callback because the cache key is not filled yet.

### `route_is()`

Determine whether the current route's name matches the given patterns.

```php
if (route_is('dahsboard.*')) {
    return 'You are in the dashboard';
}
```

### `shadow()`

Calls a method on an object if it exists, or returns false. It supports Macros.

```php
if ($rendered = shadow($mayRender, 'render')) {
    return $rendered;
}

return response((string)$mayRender);
```

### `sleep_between()`

Runs a callback while sleeping between multiple executions, returning a Collection of all results.

```php
use App\Models\User;

sleep_between(3, 1000, fn() => User::query()->inRandomOrder()->value('name'))

// [
//     'john',
//     'michel',
//     'maria',
// ]
```

### `taptap()`

Call the given Closure with the given value then return the value, twice.

```php
use App\Models\User;
use App\Notifications\Message;

return taptap(User::find(1))->notify(new Message('Hello!'))->save();
```

### `undot_path()`

Transforms a path from dot notation to a relative path.

```php
$path = undot_path('files.user_id_312.videos');

// files/user_id_312/videos/
```

### `until()`

Returns the interval from a date until the desired date.

```php
until('now', 'next month')->total('days');

// 25
```

### `user()`

Returns the currently authenticated user, if any.

```php
user()?->name;

// "John Doe"
```

### `weekend()`

Returns the end of the week.  It supports setting what day the week end.

```php
weekend()->toDateTimeString();

// 2015-07-05 23:59:59

weekend('now')->toDateTimeString();

// 2015-07-13 23:59:59
```

### `weekstart()`

Returns the end of the week. It supports setting what day the week starts.

```php
weekstart()->toDateTimeString();

// 2015-06-28 00:00:00

weekstart('now')->toDateTimeString();

// 2015-07-06 00:00:00
```

### `which_of()`

Returns the key of the option which comparison or callback returns true.

```php
$which = which_of('foo', [0 => 'baz', 1 => 'bar', 2 => 'foo'])

// 2
```

If the callback returns something truthy, that value will be used.

```php
$which = which_of(
    'foo',
    ['baz', 'bar', 'foo'], 
    fn($subject, $option) => $subject === $option ? 'cougar' : false)
);

// "cougar"
```

### `yesterday()`

Returns the date for yesterday.

```php
// 2015-07-12 17:30:00
yesterday();

// 2015-07-11 00:00:00
```

## Missing a helper?

If you have an idea for a helper, shoot it as an issue. PR with test and good code quality receive priority.

## Security

If you discover any security related issues, please email darkghosthunter@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
