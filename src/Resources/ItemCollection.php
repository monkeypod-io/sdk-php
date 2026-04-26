<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

/**
 * @method  static  withActive(bool $active)
 * @method  static  withName(string $name)
 */
class ItemCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    public function getBaseEndpoint(): string
    {
        return (new Item($this->apiClient))->getBaseEndpoint();
    }

    protected function buildResource(array $data): Resource
    {
        return (new Item())->set(null, $data);
    }
}
