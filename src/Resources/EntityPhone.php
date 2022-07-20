<?php

namespace MonkeyPod\Api\Resources;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

class EntityPhone implements Resource
{
    use ActsAsResource;

    /**
     * @throws IncompleteConfigurationException
     * @throws InvalidUuidException
     */
    public static function getEndpoint(Client $client, ...$parameters): string
    {
        $entityUuid = array_shift($parameters);
        if (! Str::isUuid($entityUuid)) {
            throw new InvalidUuidException();
        }

        $phoneUuid = count($parameters)
            ? array_shift($parameters)
            : '';

        if (! empty($phoneUuid) && ! Str::isUuid($phoneUuid)) {
            throw new InvalidUuidException();
        }

        return $client->getBaseUri() . "entities/{$entityUuid}/phones/{$phoneUuid}";
    }
}