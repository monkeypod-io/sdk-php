<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Concerns\AttachedToEntity;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

class EntityInteractionCollection implements ResourceCollection
{
    use ActsAsResourceCollection,
        AttachedToEntity;

    protected function buildResource(array $data): Resource
    {
        $interaction = $this->entity->interaction();
        $interaction->set(null, $data);

        return $interaction;
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return $this->entity->interaction()->getBaseEndpoint();
    }
}