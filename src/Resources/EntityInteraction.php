<?php

namespace MonkeyPod\Api\Resources;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidResourceException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Concerns\AttachedToEntity;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string     $type           The type of interaction (e.g., "Phone Call"). (Required)
 * @property string     $description    A description of what happened. (Required)
 * @property ?string    $link           A link to more or related information
 * @property string     $happened_at    Datetime string (in UTC) when it happened. (Required)
 * @property ?int       $flag           An "important" flag. 1 = Negative, 2 = Neutral, 3 = Positive. Leave null for regular importance
 */
class EntityInteraction implements Resource
{
    use ActsAsResource,
        AttachedToEntity;

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . "entities/{$this->entity->id}/interactions";
    }
}