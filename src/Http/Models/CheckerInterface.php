<?php

namespace Zanichelli\HealthCheck\Http\Models;


interface CheckerInterface
{
    public function check(): Status;
}
