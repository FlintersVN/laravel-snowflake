<?php

namespace Septech\Snowflake\Console\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\Command;

class WorkerTokenCommand extends Command
{
    protected $signature = 'worker:token {--force : Overrides the generated token}';

    protected $description = 'Generate server token using to release & assign worker via API';

    public function handle()
    {
        $env = new Env($this->laravel->environmentFilePath());

        $write = ! $env->has("SNOWFLAKE_HTTP_TOKEN") || $this->option('force');

        if ($write || $this->confirm("The token was assigned. Do you want overrides it?")) {
            $token = Str::random(64);

            $env->put("SNOWFLAKE_HTTP_TOKEN", $token);

            $this->line("Token: <comment>$token</comment>");
            $this->info("Application key set successfully.");
        }
    }
}
