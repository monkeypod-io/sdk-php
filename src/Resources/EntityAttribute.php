<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

class EntityAttribute implements Resource
{
    use ActsAsResource;

    public function getBaseEndpoint(): string
    {
        throw new \InvalidArgumentException("Endpoint not supported");
    }
}