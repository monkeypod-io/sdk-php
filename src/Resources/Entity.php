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
 * @property array&EntityPhone[] $phones
 */
class Entity implements Resource
{
    use ActsAsResource;

    public function hydrateNestedResources(): void
    {
        foreach ($this->get("phones") ?? [] as $key => $value) {
            $phone = new EntityPhone($value['id']);
            $phone->set(null, $value);

            $this->set("phones.$key", $phone);
        }
    }

    /**
     * @throws InvalidResourceException
     * @throws IncompleteConfigurationException
     * @throws ApiResponseError
     */
    public static function retrieve($uuid): static
    {
        return Client::singleton()->retrieve(self::class, $uuid);
    }

    /**
     * @throws ApiResponseError
     * @throws IncompleteConfigurationException
     * @throws InvalidResourceException
     */
    public static function create(array $data): static
    {
        return Client::singleton()->create(self::class, $data);
    }

    /**
     * @throws IncompleteConfigurationException
     * @throws InvalidUuidException
     */
    public static function getEndpoint(Client $client, ...$parameters): string
    {
        if (count($parameters)) {
            $uuid = $parameters[0];
            if (! Str::isUuid($uuid)) {
                throw new InvalidUuidException();
            }

            return $client->getBaseUri() . "entities/{$uuid}";
        }

        return $client->getBaseUri() . "entities";
    }
}