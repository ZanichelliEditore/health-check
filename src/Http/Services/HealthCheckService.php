<?php

namespace Zanichelli\HealthCheck\Http\Services;

use Zanichelli\HealthCheck\Http\Models\S3Checker;
use Zanichelli\HealthCheck\Http\Models\DatabaseChecker;
use Zanichelli\HealthCheck\Http\Models\FileSystemChecker;

class HealthCheckService
{

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
                default:
            }
        }

        return array_map(function ($item) {
            return $item->check()->getAll();
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
            $checkers[] = new S3Checker($configuration['disk_name']);
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
        return [new FileSystemChecker($configuration['volume_path'], $configuration['free_size_limit'])];
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
            $checkers[] = new DatabaseChecker($configuration['connection']);
        }

        return $checkers;
    }
}
