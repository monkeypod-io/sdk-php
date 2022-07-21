<?php

namespace MonkeyPod\Api\Resources\Contracts;

use MonkeyPod\Api\Client;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;

/**
 * @mixin ActsAsResource
 */
interface Resource
{
    public function hydrateNestedResources(): void;

    public function getBaseEndpoint(): string;

    public function getSpecificEndpoint(): string;
}