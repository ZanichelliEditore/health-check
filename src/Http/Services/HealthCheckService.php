<?php

namespace Zanichelli\HealthCheck\Http\Services;

use Zanichelli\HealthCheck\Http\Models\S3Checker;
use Zanichelli\HealthCheck\Http\Models\RedisChecker;
use Zanichelli\HealthCheck\Http\Models\DatabaseChecker;
use Zanichelli\HealthCheck\Http\Models\FileSystemChecker;

class HealthCheckService
{

    /**
     * Valuate system depending config parmameters
     *
     * @param array $checks
     * @return array
     */
    public function checkSystem(array $checks)
    {
        $checkers = [];

        foreach ($checks as $key => $configurations) {
            if (empty($configurations)) {
                continue;
            }

            switch ($key) {
                case 'db':
                    $checkers = array_merge($checkers, $this->checkDatabase($configurations));
                    break;
                case 'filesystem':
                    $checkers = array_merge($checkers, $this->checkFilesystem($configurations));
                    break;
                case 'redis':
                    $checkers = array_merge($checkers, $this->checkRedis($configurations));
                    break;
                default:
            }
        }

        return array_map(function ($item) {
            $status = $item->check();
            return [
                'service' => $status->getService(),
                'available' => $status->getAvailable(),
                'message' => $status->getMessage(),
                'metadata' => $status->getMetadata()
            ];
        }, $checkers);
    }

    /**
     * Undocumented function
     *
     * @param array $configurations
     * @return array
     */
    private function checkFilesystem(array $configurations)
    {
        $checkers = [];

        foreach ($configurations as $type => $ele) {
            if (empty($ele)) {
                continue;
            }
            switch ($type) {
                case 's3':
                    $checkers = array_merge($checkers, $this->checkS3($ele));
                    break;
                case 'local':
                    $checkers = array_merge($checkers, $this->checkLocal($ele));
                    break;
                default:
            }
        }

        return $checkers;
    }

    /**
     * Check S3 connections
     *
     * @param array $configurations
     * @return array
     */
    private function checkS3(array $configurations)
    {
        $checkers = [];
        foreach ($configurations as $configuration) {
            if (!empty($configuration['disk_name'])) {
                $checkers[] = new S3Checker($configuration['disk_name']);
            }
        }

        return $checkers;
    }

    /**
     * Check Local connections
     *
     * @param array $configurations
     * @return array
     */
    private function checkLocal(array $configuration)
    {
        if (!isset($configuration['free_size_limit'])) {
            return [new FileSystemChecker($configuration['disk_name'], $configuration['volume_path'])];
        }
        return [new FileSystemChecker($configuration['disk_name'], $configuration['volume_path'], $configuration['free_size_limit'])];
    }

    /**
     * Check Database connections
     *
     * @param array $configurations
     * @return array
     */
    private function checkDatabase(array $configurations)
    {
        $checkers = [];
        foreach ($configurations as $configuration) {
            if (!empty($configuration['connection'])) {
                $checkers[] = new DatabaseChecker($configuration['connection']);
            }
        }

        return $checkers;
    }

    /**
     * Check Redis connections
     *
     * @param array $configurations
     * @return array
     */
    private function checkRedis(array $configurations)
    {
        $checkers = [];
        foreach ($configurations as $configuration) {
            if (!empty($configuration['connection'])) {
                $checkers[] = new RedisChecker($configuration['connection']);
            }
        }

        return $checkers;
    }
}
