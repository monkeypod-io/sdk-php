<?php

namespace MonkeyPod\Api\Resources\Concerns;

use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Entity;

/**
 * @mixin ActsAsResource
 */
trait AttachedToEntity
{
    protected Entity $entity;

    public static function forEntity(Entity $entity, ?string $resourceId = null): static
    {
        $resource = new static;
        $resource->apiClient = $entity->getApiClient();
        $resource->entity = $entity;

        if ($resourceId && is_a(static::class, Resource::class, true)) {
            $resource->id = $resourceId;
        }

        return $resource;
    }
}