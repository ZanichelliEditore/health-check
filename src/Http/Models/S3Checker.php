<?php

namespace Zanichelli\HealthCheck\Http\Models;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Zanichelli\HealthCheck\Http\Models\Status;

class S3Checker implements CheckerInterface
{
    private const SERVICE_NAME = 'awsFileSystem';
    private $diskName;

    public function __construct(string $diskName)
    {
        $this->diskName = $diskName;
    }

    public function check(): Status
    {
        $status = new Status(self::SERVICE_NAME . '/' . $this->diskName);

        try {
            Storage::disk($this->diskName);
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $status->setAvailable(false);
            $status->setMessage(trans('healthcheck::messages.ErrorConnectionS3'));
        }

        return $status;
    }
}
