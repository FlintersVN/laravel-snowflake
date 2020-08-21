<?php

namespace Septech\Snowflake;

use Godruoyi\Snowflake\Snowflake;
use Godruoyi\Snowflake\LaravelSequenceResolver;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use Septech\Snowflake\Console\Commands\WorkerAllocateCommand;

class SnowflakeServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->registerSnowflakeInstance();

        // load migrations
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        // Merge config
        $this->mergeConfigFrom(__DIR__ . '/config/snowflake.php', 'snowflake');

        // Register publish files
        $this->publishes([
            __DIR__ . '/config/snowflake.php' => config_path('snowflake.php')
        ], 'snowflake');

        // Register commands
        $this->commands([WorkerAllocateCommand::class]);
    }

    protected function registerSnowflakeInstance()
    {
        $this->app->singleton(Snowflake::class, function ($app) {
            /** @var Application $app */
            $config = $app['config'];

            $instance = new Snowflake(
                $config->get('snowflake.datacenter_id'),
                $config->get('snowflake.worker_id')
            );

            $instance->setStartTimeStamp(strtotime($config->get('snowflake.epoch')));

            if (! $app->runningUnitTests() && ! $app->isLocal()) {
                $cacheStore = $app->make('cache')->store($config->get('snowflake.cache_store'));
                $instance->setSequenceResolver(new LaravelSequenceResolver($cacheStore));
            }

            return $instance;
        });
    }
}
