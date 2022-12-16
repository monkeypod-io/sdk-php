<?php

namespace MonkeyPod\Api\Resources;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Concerns\AttachedToEntity;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string     $id             The subscription UUID
 * @property string     $entity_id      The entity's UUID
 * @property string     $email_list_id  The mailing list's UUID
 * @property string     $created_at     An ISO 8601 formatted timestamp when the record was created
 * @property string     $updated_at     An ISO 8601 formatted timestamp when the record was last updated
 */
class EntitySubscription implements Resource
{
    use ActsAsResource,
        AttachedToEntity;
}