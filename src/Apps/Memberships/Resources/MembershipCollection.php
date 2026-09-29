<?php

namespace MonkeyPod\Api\Apps\Memberships\Resources;

use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Concerns\AttachedToEntity;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

/**
 * Lists memberships. Use `MembershipCollection::forEntity($entity)` to list the
 * memberships belonging to one entity, or `new MembershipCollection()` to list
 * every membership in the organization.
 *
 * @method static withStatus(string $status)                   Filter by status ("Active" or "Inactive")
 * @method static withMembershipLevel(string $membershipLevelId) Filter by membership level UUID
 * @method static withPaidThroughStart(string $date)           Filter to memberships paid through on or after this date (YYYY-MM-DD)
 * @method static withPaidThroughEnd(string $date)             Filter to memberships paid through on or before this date (YYYY-MM-DD)
 */
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
        return isset($this->entity)
            ? Client::singleton()->getBaseUri() . "entities/{$this->entity->id}/memberships"
            : Client::singleton()->getBaseUri() . "memberships";
    }
}
