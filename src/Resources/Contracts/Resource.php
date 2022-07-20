<?php

namespace MonkeyPod\Api\Resources\Contracts;

use MonkeyPod\Api\Client;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;

/**
 * @mixin ActsAsResource
 */
interface Resource
{
    public static function getEndpoint(Client $client, ...$parameters): string;
}