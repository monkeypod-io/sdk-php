<?php

namespace MonkeyPod\Api\Resources\Transactions;

use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

/**
 * @method  static  withStartDate(string $date)
 * @method  static  withEndDate(string $date)
 * @method  static  withFiscalSponsee(string $subdomain)
 */
class PledgeCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    public function getBaseEndpoint(): string
    {
        return (new Pledge($this->apiClient))->getBaseEndpoint();
    }

    protected function buildResource(array $data): Resource
    {
        return (new Pledge())->set(null, $data);
    }
}
