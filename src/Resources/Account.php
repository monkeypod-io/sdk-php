<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Exception\ResourceNotFoundException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property    string      $id                 The account's UUID
 * @property    ?string     $parent_id          The UUID of the account's parent
 * @property    string      $name               The account name
 * @property    ?int        $number             The account number
 * @property    string      $type               The account type
 * @property    string      $subtype            The account subtype
 * @property    boolean     $checking           Whether checks may be written from the account
 * @property    string      $current_balance    The current balance of the account (in US dollars)
 */
class Account implements Resource
{
    use ActsAsResource;

    public function retrieveByName(string $name): static
    {
        $collection = new AccountCollection();
        $collection->withName($name);
        $collection->retrieve();

        if (1 !== $collection->count()) {
            throw new ResourceNotFoundException();
        }

        $this->data = $collection->first()->get();
        $this->hydrateNestedResources();

        return $this;
    }

    public function retrieveByNumber(int $number): static
    {
        $collection = new AccountCollection();
        $collection->withNumber($number);
        $collection->retrieve();

        if (1 !== $collection->count()) {
            throw new ResourceNotFoundException();
        }

        $this->data = $collection->first()->get();
        $this->hydrateNestedResources();

        return $this;
    }

    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . 'accounts';
    }
}