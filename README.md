# README

## Installation

```php
composer require septech-laravel/snowflake

# Public config
php artisan vendor:publish --provider="Septech\\Snowflake\\SnowflakeServiceProvider"
```

## Environment

```

# 
SNOWFLAKE_EPOCH="2019-07-01 00:00:00"

```

## Usages

```php

use Septech\Snowflake\Facades\Snowflake;

Snowflake::next(); // Alias for id()
Snowflake::id();
Snowflake::parseId('6696174721395998720');

```

## Zookeeper

To use the Zookeeper we need a DB connection to keep tracking assigned IDs & machine owned that ID.

### Migration database

```sh
# Migrate database schema
php artisan migrate

# Assign the worker id to .env for current marchine.
# It will take the hostname from AWS or get the hostname by gethostname()
php artisan worker:allocate
```

### API


#### Register routes

```php
// -- routes/api.php
use Septech\Snowflake\Facades\Snowflake;

Snowflake::routes();
```

#### Wrap routes within group

```php
use Septech\Snowflake\Facades\Snowflake;

Route::group(['prefix' => '/snowflake'], function () {
    Snowflake::routes();
});

```

#### Apply the built-in middleware

We provides a simple middleware to protect the API. The middleware will take token from the `Authorization` header. Or from $_GET['token'] or $_POST['token']. Then compare with a secret key set by config `snowflake.sever_to_server_token` or `env('SNOWFLAKE_HTTP_TOKEN')`. You can set the key manually or run command

```
php artisan worker:token
```

**Force it to override**
```
php artisan worker:token --force
```

```php
Route::group(['middleware' => \Septech\Snowflake\Http\Middleware\ServerToken::class], function () {
    Snowflake::routes();
});

```

#### Using Kernel routeMiddleware

```php
// app/Http/Kernel.php
protected $routeMiddleware = [
    // ...
    'server_token' => \Septech\Snowflake\Http\Middleware\ServerToken::class
    // ...
];

// routes/api.php
Route::group(['middleware' => 'server_token'], function () {
    Snowflake::routes();
});
```
