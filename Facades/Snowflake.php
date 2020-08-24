<?php

namespace Septech\Snowflake\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array parseId(string $id, $transform = false)
 * @method static string id()
 */
class Snowflake extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Godruoyi\Snowflake\Snowflake::class;
    }

    public static function next()
    {
        return static::getFacadeRoot()->id();
    }
}
