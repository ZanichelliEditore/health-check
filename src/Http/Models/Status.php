<?php

namespace Zanichelli\HealthCheck\Http\Models;

class Status
{
    private $service;
    private $available;
    private $message;
    private $metadata;

    public function __construct(string $service, bool $available = true, ?string $message = null, array $metadata = [])
    {
        $this->service =  $service;
        $this->available =  $available;
        $this->message =  $message;
        $this->metadata =  $metadata;
    }

    public function getService()
    {
        return $this->service;
    }

    public function getAvailable()
    {
        return $this->available;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getMetadata()
    {
        return $this->metadata;
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
