<?php

namespace MonkeyPod\Api\Resources;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string     $id             The phone's UUID
 * @property string     $type           The type of phone number (e.g., "Cell" or "Work")
 * @property string     $number         A formatted phone number
 * @property string     $created_at     An ISO 8601 formatted timestamp when the record was created
 * @property string     $updated_at     An ISO 8601 formatted timestamp when the record was last updated
 */
class EntityPhone implements Resource
{
    use ActsAsResource;

    public Entity $entity;

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return Client::singleton()->getBaseUri() . "entities/{$this->entity->id}/phones";
    }
}