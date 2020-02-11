<?php

namespace Zanichelli\HealthCheck\Http\Controllers;

use Illuminate\Support\Facades\Response;
use Zanichelli\HealthCheck\Http\Services\HealthCheckService;
use Illuminate\Routing\Controller;

class HealthController extends Controller
{
    private $checks;

    public function __construct()
    {
        $this->checks = config('healthcheck');
    }

    /**
     * List messages about healtch checks chosen
     * @return Response
     */
    public function index()
    {

        $service = new HealthCheckService();

        $data = $service->checkSystem($this->checks);
        return Response::make(['status' => $data], 200);
    }
}
