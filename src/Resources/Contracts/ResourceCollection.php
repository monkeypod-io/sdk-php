<?php

namespace MonkeyPod\Api\Resources\Contracts;

use MonkeyPod\Api\Client;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;

/**
 * @mixin ActsAsResource
 */
interface ResourceCollection extends \IteratorAggregate, \Countable
{
    public function getBaseEndpoint(): string;
}