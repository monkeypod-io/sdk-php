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
class DonationCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    public function getBaseEndpoint(): string
    {
        return (new Donation($this->apiClient))->getBaseEndpoint();
    }

    protected function buildResource(array $data): Resource
    {
        return (new Donation())->set(null, $data);
    }
}
