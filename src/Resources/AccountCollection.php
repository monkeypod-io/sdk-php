<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

/**
 * @method  static  withName(string $name)
 * @method  static  withNumber(string $number)
 * @method  static  withType(string $type)
 * @method  static  withSubtype(string $type)
 */
class AccountCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    public function getBaseEndpoint(): string
    {
        return (new Account($this->apiClient))->getBaseEndpoint();
    }

    protected function buildResource(array $data): Resource
    {
        return (new Account())->set(null, $data);
    }
}