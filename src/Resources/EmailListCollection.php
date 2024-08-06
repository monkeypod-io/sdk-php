<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

/**
 * @method  static  withName(string $name)
 */
class EmailListCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    public function getBaseEndpoint(): string
    {
        return (new EmailList($this->apiClient))->getBaseEndpoint();
    }

    protected function buildResource(array $data): Resource
    {
        return (new EmailList())->set(null, $data);
    }
}
