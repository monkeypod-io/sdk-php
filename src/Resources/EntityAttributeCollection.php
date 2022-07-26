<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

class EntityAttributeCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    /**
     * @throws InvalidUuidException
     * @throws IncompleteConfigurationException
     */
    protected function buildResource(array $data): Resource
    {
        $attribute = new EntityAttribute($this->apiClient);
        $attribute->set(null, $data);

        return $attribute;
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . "entity_attributes";
    }
}