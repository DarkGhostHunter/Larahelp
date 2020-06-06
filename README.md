![Regine Tholen - Unsplash (UL) #ojGvj7CE5OQ](https://images.unsplash.com/photo-1574246915327-8cf501d94757?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&h=400&q=80)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/darkghosthunter/larahelp.svg?style=flat-square)](https://packagist.org/packages/darkghosthunter/larahelp)
[![Build Status](https://img.shields.io/travis/darkghosthunter/larahelp/master.svg?style=flat-square)](https://travis-ci.org/darkghosthunter/larahelp)
[![Quality Score](https://img.shields.io/scrutinizer/g/darkghosthunter/larahelp.svg?style=flat-square)](https://scrutinizer-ci.com/g/darkghosthunter/larahelp)
[![Total Downloads](https://img.shields.io/packagist/dt/darkghosthunter/larahelp.svg?style=flat-square)](https://packagist.org/packages/darkghosthunter/larahelp)

# Larahelp

Supercharge your Laravel projects with more than 35 useful global helpers.

## Installation

You can install the package via composer:

```bash
composer require darkghosthunter/larahelp
```

## Usage

This package includes helpful global helpers for your project, separated into different categories:

### General purpose

* `collect_lazy`: Creates a new Lazy Collection.
* `collect_times`: Create a new collection by invoking the callback a given amount of times.
* `data_transform`: Transform an item of an array using a callable.
* `enclose`: Wraps a value into a Closure.
* `fluent`: Creates a new Fluent instance.
* `pipeline`: Sends an object through a pipeline.
* `random_unique`: Returns a unique amount of results from a random generator executed a number of times.
* `none_of`: Checks if the none of the options compared or called returns true.
* `throttle`: Throttles a given callback by a key.
* `unless`: Returns a value when a condition is falsy.
* `swap_vars`: Swap two variables values, and returns the second argument value.
* `when`: Returns a value when a condition is truthy.
* `which_of`: Returns the first value which comparison or callback returns true.
* `while_sleep`: Executes an operation while sleeping milliseconds between multiple executions.

### Datetime

* `diff`: Returns the difference between two dates.
* `from_now`: Creates a datetime with an interval of time from now.
* `period`: Returns the period of a given start and end or interval.
* `until_now`: Creates a datetime with an interval of time until now.
* `yesterday`: Create a new Carbon instance for the day before today.

### Filesystem

* `base_path_of`: Return the relative path of a class from the project root path.
* `class_defined_at`: Returns where the file path where the object was defined.
* `dot_path`: Returns a path in dot notation.
* `undot_path`: Returns a path from the base path in dot notation.

### HTTP

* `routed` : Returns the current route of the HTTP Request, or null when none.
* `routed_is`: Determine whether the current route's name matches the given patterns.
* `created`: Return an HTTP 201 response (OK, Created) with the content recently created.
* `http`: Sends an HTTP Request. A pending Request is returned if no verb is issued.
* `ok`: Returns an HTTP 204 response (OK, No Content).

### Objects

* `arguments_of`: Returns a collection of arguments received by the given callable.
* `call_existing`: Calls a dynamic method or macro if it exists in the object instance.
* `replicate`: Replicates an object.
* `has_trait`: Checks recursively if the object is using a trait.
* `map_unto`: Instance items into objects passing the item as constructor or static method call parameter.
* `methods_of`: Returns a collection of all methods from a given class or object.
* `missing_trait`: Checks recursively if the object is not using a trait.
* `properties_of`: Returns a collection of all properties from a given class or object.

### Services

* `artisan`:  Calls an Artisan command, or return the Artisan console instance.
* `hasher`: Hashes a value. If no value is given, a Hasher is returned.
* `user`: Returns the current user authenticated, or null if is a guest.

> This package is focused on the backend. If you want views helpers, I recommend you to use [custom Blade directives](https://laravel.com/docs/blade#extending-blade) instead.

### Missing a helper?

If you have an idea for a helper, shoot it as an issue. PR with test and good code quality receive priority.

### Security

If you discover any security related issues, please email darkghosthunter@gmail.com instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.