<?php

namespace Zanichelli\HealthCheck\Http\Models;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Zanichelli\HealthCheck\Http\Constants\Service;
use Zanichelli\HealthCheck\Http\Models\Status;

class FileSystemChecker implements CheckerInterface
{
    private $diskName;
    private $path;
    private $limitThreshold;

    public function __construct(string $diskName = 'local', string $path, int $limitThreshold = 500)
    {
        $this->diskName = $diskName;
        $this->path = $path;
        $this->limitThreshold = $limitThreshold * 1024 * 1024;
    }

    public function check(): Status
    {
        $status = new Status(Service::FILESYSTEM . '/' . $this->diskName);

        try {
            $freeSpace = disk_free_space($this->path);
            $saved = Storage::disk($this->diskName)->put('healthcheck.temp', 'Contents');

            if (!$saved) {
                Log::error("Health check failed: could not save healthcheck.temp");
                $status->setAvailable(false);
                $status->setMessage(trans('healthcheck::messages.filesystem.WritingError'));
            } else {
                Storage::disk($this->diskName)->delete('healthcheck.temp');
            }
        } catch (Exception $e) {
            Log::error("Health check failed: " . $e->getMessage());
            $status->setAvailable(false);
            $status->setMessage(trans('healthcheck::messages.DiskNotAvailable'));

            return $status;
        }

        if ($freeSpace < $this->limitThreshold) {
            $status->setMetadata([
                'freespace' => [
                    'size' => (int) (($freeSpace / 1024) / 1024),
                    'unit' => 'Megabyte',
                    'message' => trans('healthcheck::messages.filesystem.NoDiskSpace')
                ]
            ]);
        };

        return $status;
    }
}
