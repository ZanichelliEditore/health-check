<?php

namespace Zanichelli\HealthCheck\Http\Models;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Zanichelli\HealthCheck\Http\Models\Status;

class FileSystemChecker implements CheckerInterface
{
    private $diskName;
    private $path;
    private $limitThreshold;

    public function __construct(string $path, int $limitThreshold = 500, string $diskName = 'local')
    {
        $this->diskName = $diskName;
        $this->path = $path;
        $this->limitThreshold = $limitThreshold * 1024 * 1024;
    }

    public function check(): Status
    {
        $status = new Status();
        $freeSpace = disk_free_space($this->path);

        try {
            $saved = Storage::disk($this->diskName)->put('healthcheck.temp', 'Contents');

            if (!$saved) {
                $status->setAvailable(false);
                $status->setMessage(trans('healtcheck::messages.filesystem.WritingError'));
            }
            Storage::disk($this->diskName)->delete('healthcheck.temp');
        } catch (Exception $e) {
            Log::error($e->getMessage());
            $status->setAvailable(false);
            $status->setMessage(trans('healtcheck::messages.DiskNotAvailable'));

            return $status;
        }

        if ($freeSpace < $this->limitThreshold) {
            $status->setMetadata([
                'freespace' => [
                    'size' => ($freeSpace / 1024) / 1024,
                    'message' => trans('healtcheck::messages.filesystem.NoDiskSpace')
                ]
            ]);
        };

        return $status;
    }
}
