<?php

namespace Zanichelli\HealthCheck\Http\Models;

use Exception;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;
use Zanichelli\HealthCheck\Http\Constants\Service;
use Zanichelli\HealthCheck\Http\Models\Status;

class RedisChecker implements CheckerInterface
{
    private $connectionName;

    public function __construct(string $connectionName)
    {
        $this->connectionName = $connectionName;
    }

    public function check(): Status
    {
        $status = new Status(Service::REDIS . '/' . $this->connectionName);

        try {
            Redis::connection($this->connectionName);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $status->setAvailable(false);
            $status->setMessage(trans('healthcheck::messages.RedisConnectionNotAvailable'));
        }

        return $status;
    }
}
