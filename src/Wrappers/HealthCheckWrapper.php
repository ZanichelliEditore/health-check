<?php

namespace Zanichelli\HealthCheck\Wrappers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class HealthCheckWrapper
{
    public function getDbConnection()
    {
        return DB::connection()->getPdo();
    }

    public function disk_free_space($volume)
    {
        return disk_free_space($volume);
    }

    public function storageDisk($storage)
    {
        return Storage::disk($storage);
    }
}
