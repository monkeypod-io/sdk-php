<?php

namespace MonkeyPod\Api\Resources;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidResourceException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Exception\UnmetDependencyException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Concerns\AttachedToEntity;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string     $id             The UUID of the interaction
 * @property string     $entity_id      The UUID of the associated entity
 * @property string     $type           The type of interaction (e.g., "Phone Call"). (Required)
 * @property string     $description    A description of what happened. (Required)
 * @property ?string    $link           A link to more or related information
 * @property string     $happened_at    Datetime string (in UTC) when it happened. (Required)
 * @property ?int       $flag           An "important" flag. 1 = Negative, 2 = Neutral, 3 = Positive. Leave null for regular importance
 * @property string     $created_at     An ISO 8601 formatted timestamp when the interaction record was created
 * @property string     $updated_at     An ISO 8601 formatted timestamp when the interaction record was last updated
 */
class EntityInteraction implements Resource
{
    use ActsAsResource,
        AttachedToEntity;
}