<?php

namespace MonkeyPod\Api\Apps\Memberships\Resources;

use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Concerns\AttachedToEntity;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

class MembershipCollection implements ResourceCollection
{
    use ActsAsResourceCollection;
    use AttachedToEntity;

    /**
     * @throws InvalidUuidException
     * @throws IncompleteConfigurationException
     */
    protected function buildResource(array $data): Resource
    {
        $member = new Membership($this->apiClient);
        $member->set(null, $data);

        return $member;
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return Client::singleton()->getBaseUri() . "entities/{$this->entity->id}/memberships";
    }
}