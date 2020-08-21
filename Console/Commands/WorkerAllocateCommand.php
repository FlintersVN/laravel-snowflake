<?php

namespace Septech\Snowflake\Console\Commands;

use Illuminate\Console\Command;
use Septech\Snowflake\AllocatedWorker;

class WorkerAllocateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'worker:allocate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Allocate worker id & data center for current instance';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $machineId = $this->getInstanceId();

        $instanceId = AllocatedWorker::allocateFor($machineId);

        if (! $this->hasEnvironment('SNOWFLAKE_WORKER_ID')) {
            $workerId = AllocatedWorker::workerId($instanceId);

            $this->info("Assigning worker:" . json_encode(["machine_id" => $machineId, 'worker_id' => $workerId]));
            $this->putEnvironment('SNOWFLAKE_WORKER_ID', $workerId);
        }

        if (! $this->hasEnvironment('SNOWFLAKE_DATACENTER_ID')) {
            $dataCenterId = AllocatedWorker::dataCenterId($instanceId);

            $this->info("Assigning data center:" . json_encode(["machine_id" => $machineId, 'data_center_id' => $dataCenterId]));
            $this->putEnvironment('SNOWFLAKE_DATACENTER_ID', $dataCenterId);
        }

        $this->info('OK');
    }

    protected function putEnvironment($key, $value)
    {
        $envPath = app()->environmentFilePath();
        file_put_contents($envPath, sprintf("%s=\"%s\"\n", $key, $value), FILE_APPEND);
    }

    protected function hasEnvironment($key)
    {
        return env($key, $this) !== $this;
    }

    protected function getInstanceId()
    {
        if (app()->environment('local')) {
            return gethostname();
        }

        // This is endpoint to get instance id from AWS. All instance have same URL
        $curl = curl_init('http://169.254.169.254/latest/meta-data/instance-id');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        return curl_exec($curl) ?: gethostname();
    }
}
