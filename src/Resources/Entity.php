<?php

namespace MonkeyPod\Api\Resources;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidResourceException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string                 $id
 * @property array&EntityPhone[]    $phones
 */
class Entity implements Resource
{
    use ActsAsResource;

    public function interaction($interactionId = null): EntityInteraction
    {
        return EntityInteraction::forEntity($this, $interactionId);
    }

    public function interactions(): EntityInteractionCollection
    {
        return EntityInteractionCollection::forEntity($this);
    }

    /**
     * @throws InvalidUuidException
     * @throws IncompleteConfigurationException
     */
    public function hydrateNestedResources(): void
    {
        foreach ($this->get("phones") ?? [] as $key => $value) {
            $phone = new EntityPhone($value['id']);
            $phone->set(null, $value);

            $this->set("phones.$key", $phone);
        }
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . "entities";
    }
    
    public function getName(): string
    {
        if (isset($this->data['type']) && $this->data['type'] !== 'Individual') {
            return $this->data['organization_name'];
        }
        
        return str($this->data['first_name'] ?? '')
            ->append(' ')
            ->append($this->data['last_name'] ?? '')
            ->trim()
            ->toString();
    }
}