<?php

namespace MonkeyPod\Api\Resources\Transactions;

use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

/**
 * @method  static  withStartDate(string $date)
 * @method  static  withEndDate(string $date)
 */
class SaleCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    public function getBaseEndpoint(): string
    {
        return (new Sale($this->apiClient))->getBaseEndpoint();
    }

    protected function buildResource(array $data): Resource
    {
        return (new Sale())->set(null, $data);
    }
}
