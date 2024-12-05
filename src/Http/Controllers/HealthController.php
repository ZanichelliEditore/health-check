<?php

namespace Zanichelli\HealthCheck\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Zanichelli\HealthCheck\Http\Services\HealthCheckService;
use Illuminate\Routing\Controller;

class HealthController extends Controller
{
    private $checks;
    private $healthService;

    public function __construct(HealthCheckService $healthService)
    {
        $this->checks = config('healthcheck');
        $this->healthService = $healthService;
    }

    /**
     * List messages about healtch checks chosen
     * @return Response
     */
    public function index()
    {

        $data = $this->healthService->checkSystem($this->checks);

        if (empty($data)) {
            return Response::make([], 204);
        }

        $finalStatus = array_reduce($data, function ($accumulator, $item) {
            return $accumulator && $item['available'];
        }, true);

        if (!$finalStatus) {
            Log::error("Health check failed", ["healthCheckStatus" => $data]);
            return Response::make(['status' => $data], 503);
        }
        return Response::make(['status' => $data], 200);
    }
}
