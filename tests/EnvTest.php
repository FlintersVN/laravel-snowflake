<?php

namespace Tests;

use Illuminate\Support\Str;
use PHPUnit\Framework\TestCase;
use Septech\Snowflake\Console\Commands\Env;

class EnvTest extends TestCase
{
    public function test_parse()
    {
        $env = new Env(__DIR__ . '/stubs/.env');

        $env->replace("SOME_KEY", Str::random("6"));

        $this->assertTrue(false);
    }
}