<?php

namespace MonkeyPod\Api\Resources\Concerns;

use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\UnmetDependencyException;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Entity;

/**
 * @mixin ActsAsResource
 */
trait AttachedToEntity
{
    protected Entity $entity;

    public function attachToEntity(Entity $entity): static
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * @throws IncompleteConfigurationException
     * @throws UnmetDependencyException
     */
    public function getBaseEndpoint(): string
    {
        if (! isset($this->entity) || ! $this->entity->id) {
            throw new UnmetDependencyException("An entity resource with an ID must be present first");
        }

        return $this->entity->getSpecificEndpoint() . "/" . $this->getBaseEndpointSegment();
    }
    /**
     * Returns the string segment that is appended to the entity's specific endpoint
     * to create the attached resource's base endpoint.
     *
     * @return string
     */
    protected function getBaseEndpointSegment(): string
    {
        return str(class_basename($this))
            ->replaceFirst("Entity", "")
            ->lower()
            ->plural()
            ->toString();
    }

    public static function forEntity(Entity $entity, ?string $resourceId = null): static
    {
        $resource = new static($entity->getApiClient());
        $resource->apiClient = $entity->getApiClient();
        $resource->entity = $entity;

        if ($resourceId && is_a(static::class, Resource::class, true)) {
            $resource->id = $resourceId;
        }

        return $resource;
    }
}