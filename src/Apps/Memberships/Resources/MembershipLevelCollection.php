<?php

namespace MonkeyPod\Api\Apps\Memberships\Resources;

use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Concerns\AttachedToEntity;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

class MembershipLevelCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    /**
     * @throws InvalidUuidException
     * @throws IncompleteConfigurationException
     */
    protected function buildResource(array $data): Resource
    {
        $level = new MembershipLevel($this->apiClient);
        $level->set(null, $data);

        return $level;
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return Client::singleton()->getBaseUri() . "membership-levels";
    }
}