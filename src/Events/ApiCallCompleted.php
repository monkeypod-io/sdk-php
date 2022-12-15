<?php

namespace MonkeyPod\Api\Events;

use Illuminate\Foundation\Events\Dispatchable;

class ApiCallCompleted
{
    use Dispatchable;

    public function __construct(
        public string $endpoint,
        public string $method,
        public mixed $response,
    ) {}
}