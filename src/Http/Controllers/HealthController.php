<?php

namespace Zanichelli\HealthCheck\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use Zanichelli\HealthCheck\Wrappers\HealthCheckWrapper;

class HealthController extends Controller
{
    private $checks;
    private $HealthWrapper;
    private $health = [];
    private $fail   = false;

    public function __construct(HealthCheckWrapper $healthWrapper)
    {
        $this->checks = explode(',', config('healthcheck.checks'));
        $this->HealthWrapper = $healthWrapper;
    }

    /**
     * List messages about healtch checks chosen
     * @return Response
     */
    public function index()
    {
        try {
            foreach ($this->checks as $check) {
                switch ($check) {
                    case 'db':
                        $this->checkDb();
                        break;
                    case 'localstorage':
                        $this->checkLocalStorage();
                        break;
                    case 's3':
                        $this->checkS3Storage();
                        break;
                    case 'freespace':
                        $this->checkFreeSpace();
                        break;
                    default:
                        null;
                }
            }
        } catch (\Exception $e) {
            $this->fail = true;
        }

        if ($this->fail === false) {
            Log::info('200* ');
            return Response::make(['message' => $this->getHealth()], 200);
        } else {
            Log::error('500* ' . json_encode(['content' => $this->getHealth()]));
            return Response::make(['message' => $this->getHealth()], 500);
        }
    }


    /**
     * health message getter
     * 
     * @return array $health
     */
    public function getHealth()
    {
        return $this->health;
    }


    /**
     * Wrapper to check the local storage is writable
     */
    public function checkLocalStorage()
    {
        return $this->checkStorage('local');
    }

    /**
     * Tries to write and deletes a file on the selected storage
     * 
     * @param string $storage
     * @return null
     */
    private function checkStorage(string $storage)
    {
        try {
            if ($this->HealthWrapper->storageDisk($storage)->put('file.txt', 'Contents')) {
                $this->HealthWrapper->storageDisk($storage)->delete('file.txt', 'Contents');
                $this->health[$storage . 'storage']['status'] = 'ok';
            } else {
                $this->health[$storage . 'storage']['status'] = 'ko';
                $this->health[$storage . 'storage']['message'] = trans('healthcheck::messages.UnableToWriteOnDisk', ['storage' => $storage]);
                $this->fail = true;
            }
        } catch (\Exception $e) {
            $this->health[$storage . 'storage']['status'] = 'ko';
            $this->health[$storage . 'storage']['message'] = $e->getMessage();
            $this->fail = true;
        }
        return;
    }

    /**
     * Verifies that the DB connection is returned correctly
     * @return null
     */
    public function checkDb()
    {
        try {
            if ($this->HealthWrapper->getDbConnection()) {
                $this->health['db']['status'] = 'ok';
            } else {
                $this->health['db']['status'] = 'ko';
                $this->health['db']['message'] = trans('healthcheck::messages.UnableToWriteOnDisk');
                $this->fail = true;
            }
        } catch (\Exception $e) {
            $this->health['db']['status'] = 'ko';
            $this->health['db']['message'] = $e->getMessage();
            $this->fail = true;
        }
        return;
    }

    /**
     * Verifies that the space left on the volume is more than 1Gb
     * @return null
     */
    public function checkFreeSpace()
    {
        if ($this->HealthWrapper->disk_free_space(config('healthcheck::params.volume_path')) < config('healthcheck::params.free_size_limit')) {
            $this->health['freeSpace']['status'] = 'ko';
            $this->health['freeSpace']['message'] = trans('healthcheck::messages.NoDiskSpace', ['space' => $this->HealthWrapper->disk_free_space(config('healthcheck::params.volume_path'))]);
            $this->fail = true;
        } else {
            $this->health['freeSpace']['status'] = 'ok';
        }
        return;
    }

    /**
     * Tries to get a list of files from the s3 attachments directory
     * @return null
     */
    public function checkS3Storage()
    {
        try {
            $files = $this->HealthWrapper->storageDisk('s3')->files('attachments');
            $this->health['s3storage']['status'] = 'ok';
        } catch (\Exception $e) {
            $this->health['s3storage']['status'] = 'ko';
            $this->health['s3storage']['message'] = $e->getMessage();
            $this->fail = true;
        }
        return;
    }
}
