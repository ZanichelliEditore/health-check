<?php

namespace Zanichelli\HealthCheck\Http\Models;

use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Zanichelli\HealthCheck\Http\Models\Status;

class DatabaseChecker implements CheckerInterface
{
    private const SERVICE_NAME = 'database';
    private $connectionName;

    public function __construct(string $connectionName)
    {
        $this->connectionName = $connectionName;
    }

    public function check(): Status
    {
        $status = new Status(self::SERVICE_NAME . '/' . $this->connectionName);

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
