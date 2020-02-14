<?php

namespace Zanichelli\HealthCheck\Http\Models;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Zanichelli\HealthCheck\Http\Constants\Service;
use Zanichelli\HealthCheck\Http\Models\Status;

class DatabaseChecker implements CheckerInterface
{
    private $connectionName;

    public function __construct(string $connectionName)
    {
        $this->connectionName = $connectionName;
    }

    public function check(): Status
    {
        $status = new Status(Service::DATABASE . '/' . $this->connectionName);

        try {
            DB::connection($this->connectionName)->getPdo();
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $status->setAvailable(false);
            $status->setMessage(trans('healthcheck::messages.DatabaseConnectionNotAvailable'));
        }

        return $status;
    }
}
