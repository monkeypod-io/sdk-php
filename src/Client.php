<?php

namespace MonkeyPod\Api;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidResourceException;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Entity;

class Client
{
    public static array $resourceAliases = [
        'entity' => Entity::class,
    ];

    protected string $version = "v2";

    protected string $subdomain;

    protected string $apiKey;

    protected \GuzzleHttp\Client $guzzle;

    private static Client $singleton;

    public function __construct()
    {
        $this->guzzle = new \GuzzleHttp\Client();
    }

    public function setVersion(string|int $version): static
    {
        $this->version = $version;

        return $this;
    }

    public function setSubdomain(string $subdomain): static
    {
        $this->subdomain = $subdomain;

        return $this;
    }

    public function setApiKey(string $apiKey): static
    {
        $this->apiKey = $apiKey;

        return $this;
    }

    /**
     * @throws IncompleteConfigurationException
     * @throws InvalidResourceException
     * @throws ApiResponseError
     */
    public function retrieve(string $resourceClass, ...$parameters): Resource
    {
        $this->confirmConfigured();

        if (! class_exists($resourceClass) && isset(self::$resourceAliases[$resourceClass])) {
            $resourceClass = self::$resourceAliases[$resourceClass];
        }

        if (! is_a($resourceClass, Resource::class, true)) {
            throw new InvalidResourceException();
        }

        $endpoint = $resourceClass::getEndpoint($this, ...$parameters);

        $response = (new HttpClient())
            ->withToken($this->apiKey)
            ->get($endpoint)
            ->onError(function (Response $response) {
                throw (new ApiResponseError())
                    ->setHttpStatus($response->status())
                    ->setErrors($response->json("errors", []));
            })
            ->json();

        $resource = new $resourceClass;
        $resource->setData(null, $response);

        return $resource;
    }


    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseUri(): string
    {
        $this->confirmConfigured();

        return "https://{$this->subdomain}.monkeypod.io/api/{$this->version}/";
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function confirmConfigured(): static
    {
        if (! isset($this->version, $this->subdomain, $this->apiKey)) {
            throw new IncompleteConfigurationException();
        }

        return $this;
    }

    public static function configure(string $apiKey, string $subdomain, string $version = null): static
    {
        self::$singleton = new static;

        return self::$singleton
            ->setApiKey($apiKey)
            ->setSubdomain($subdomain)
            ->setVersion($version ?? self::$singleton->version);
    }

    public static function __callStatic(string $name, array $arguments)
    {
        return self::singleton()->$name(...$arguments);
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public static function singleton(): static
    {
        if (! isset(self::$singleton)) {
            throw new IncompleteConfigurationException();
        }

        return self::$singleton;
    }
}