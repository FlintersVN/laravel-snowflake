# README

## Installation

```php
composer require septech-laravel/snowflake
```

## Usages

```php

use Septech\Snowflake\Facades\Snowflake;

Snowflake::next(); // Alias for id()
Snowflake::id();
Snowflake::parseId('6696174721395998720');

```
