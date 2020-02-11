<?php

namespace Zanichelli\HealthCheck\Http\Models;

class Status
{
    private $available;
    private $message;
    private $metadata;

    public function __construct(bool $available = true, string $message = null, array $metadata = [])
    {
        $this->available =  $available;
        $this->message =  $message;
        $this->metadata =  $metadata;
    }

    public function setAvailable(bool $available)
    {
        $this->available = $available;
    }

    public function setMessage(string $message)
    {
        $this->message =  $message;
    }

    public function setMetadata(array $metadata)
    {
        $this->metadata =  $metadata;
    }
}
