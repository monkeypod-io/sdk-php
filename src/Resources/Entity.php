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

class Entity implements Resource
{
    use ActsAsResource;

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
     * @throws IncompleteConfigurationException
     * @throws InvalidUuidException
     */
    public static function getEndpoint(Client $client, ...$parameters): string
    {
        $uuid = $parameters[0];
        if (! Str::isUuid($uuid)) {
            throw new InvalidUuidException();
        }

        return $client->getBaseUri() . "entities/{$uuid}";
    }
}