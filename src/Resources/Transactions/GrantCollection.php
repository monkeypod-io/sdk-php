<?php

namespace MonkeyPod\Api\Resources\Transactions;

use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

/**
 * @method  static  withStartDate(string $date)
 * @method  static  withEndDate(string $date)
 * @method  static  withFiscalSponsee(string $subdomain)
 * @method  static  withRestriction(bool $restricted)
 */
class GrantCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    public function getBaseEndpoint(): string
    {
        return (new Grant($this->apiClient))->getBaseEndpoint();
    }

    protected function buildResource(array $data): Resource
    {
        return (new Grant())->set(null, $data);
    }
}
