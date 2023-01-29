<?php

namespace MonkeyPod\Api\Apps\Memberships\Resources;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Concerns\AttachedToEntity;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string     $id             The membership's UUID
 * @property string     $entity_id      The UUID of the associated entity
 * @property string     $level          The membership level
 * @property string     $status         The membership's status
 * @property string     $start_date     The start date
 * @property string     $end_date       The end date
 * @property string     $created_at     An ISO 8601 formatted timestamp when the record was created
 * @property string     $updated_at     An ISO 8601 formatted timestamp when the record was last updated
 */
class Membership implements Resource
{
    use ActsAsResource;
    use AttachedToEntity;

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return Client::singleton()->getBaseUri() . "entities/{$this->entity->id}/memberships";
    }
}