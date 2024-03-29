<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResourceCollection;
use MonkeyPod\Api\Resources\Concerns\AttachedToEntity;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

/**
 * @method  Entity|null first()
 */
class EntityCollection implements ResourceCollection
{
    use ActsAsResourceCollection;

    /**
     * @param Entity $entity
     * @return EntityCollection&Entity[]
     * @throws IncompleteConfigurationException
     * @throws InvalidUuidException
     * @throws \MonkeyPod\Api\Exception\ApiResponseError
     */
    public function match(Entity $entity)
    {
        $endpoint = $entity->getBaseEndpoint() . '/match';
        $queryString = http_build_query(array_filter([
            'id' => $entity->id,
            'email' => $entity->email,
            'name' => $entity->getFullName(),
            'metadata' => $entity->getMetadata(),
        ]));

        $response = $this->apiClient->get("$endpoint?$queryString");

        return $this->fromResponseData($response);
    }

    protected function buildResource(array $data): Resource
    {
        return (new Entity($this->apiClient))->set(null, $data);
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return (new Entity($this->apiClient))->getBaseEndpoint();
    }
}