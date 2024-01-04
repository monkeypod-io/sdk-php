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
 * @property string     $id             The membership level's UUID
 * @property string     $name           The name of the membership level
 * @property string     $income_type    Either "Earned" or "Contributed"
 * @property string     $role           The name of the role associated with the membership level
 * @property string     $created_at     An ISO 8601 formatted timestamp when the record was created
 * @property string     $updated_at     An ISO 8601 formatted timestamp when the record was last updated
 */
class MembershipLevel implements Resource
{
    use ActsAsResource;

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return Client::singleton()->getBaseUri() . "membership-levels";
    }
}