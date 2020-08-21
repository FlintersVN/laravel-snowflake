<?php

namespace Septech\Snowflake;

use Illuminate\Database\Eloquent\Model;
use Septech\Snowflake\Exceptions\ReachedMaximumWorkersException;

class AllocatedWorker extends Model
{
    // Worker ID & Data Center Available from 1-31
    const MAX_WORKERS = 962;

    protected $guarded = [];

    public static function allocateFor($machineId)
    {
        $exists = static::where('instance_id', $machineId)->first();

        if ($exists) {
            return $exists->worker_id;
        }

        $workerId = static::next();

        static::updateOrCreate(
            ['worker_id' => $workerId],
            ['instance_id' => $machineId,]
        );

        return $workerId;
    }

    public static function release($instanceId)
    {
        static::where('instance_id', $instanceId)->update(['instance_id' => null,]);
    }

    public static function next()
    {
        $released = static::whereNull('instance_id')->orderBy('worker_id', 'asc')->first();

        if ($released) {
            return $released->worker_id;
        }

        $workerId = static::max('worker_id') + 1;

        if ($workerId > static::MAX_WORKERS) {
            throw new ReachedMaximumWorkersException('Cannot allocate a new worker_id. Reached maximum worker instances.');
        }

        return $workerId;
    }

    public static function workerId($instanceId)
    {
        return $instanceId % 31 ?: 31;
    }

    public static function dataCenterId($instanceId)
    {
        return ($instanceId >> 5) + 1;
    }

    public static function getInstanceCounter($workerId, $dataCenterId)
    {
        return ($dataCenterId - 1) * 31 + $workerId;
    }
}
