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
    protected string $version = "v2";

    protected string $subdomain;

    protected string $apiKey;

    protected string $apiBase = "https://monkeypod.io";

    protected HttpClient $httpClient;

    private static Client $singleton;

    public function __construct()
    {
        $this->httpClient = new HttpClient();
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

    public function setApiBase(string $url): static
    {
        $this->apiBase = $url;

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

        if (! is_a($resourceClass, Resource::class, true)) {
            throw new InvalidResourceException();
        }

        $endpoint = $resourceClass::getEndpoint($this, ...$parameters);

        $response = $this->httpClient
            ->withToken($this->apiKey)
            ->get($endpoint)
            ->onError(function (Response $response) {
                throw (new ApiResponseError())
                    ->setHttpStatus($response->status())
                    ->setErrors($response->json("errors", []));
            })
            ->json();

        /** @var Resource $resource */
        $resource = new $resourceClass;
        $resource->set(null, $response['data']);
        $resource->hydrateNestedResources();

        return $resource;
    }

    /**
     * @throws IncompleteConfigurationException
     * @throws InvalidResourceException
     * @throws ApiResponseError
     */
    public function create(string $resourceClass, array $data): Resource
    {
        $this->confirmConfigured();

        if (! is_a($resourceClass, Resource::class, true)) {
            throw new InvalidResourceException();
        }

        $endpoint = $resourceClass::getEndpoint($this);

        $response = $this->httpClient
            ->withToken($this->apiKey)
            ->post($endpoint, $data)
            ->onError(function (Response $response) {
                throw (new ApiResponseError())
                    ->setHttpStatus($response->status())
                    ->setErrors($response->json("errors", []));
            })
            ->json();

        /** @var Resource $resource */
        $resource = new $resourceClass;
        $resource->set(null, $response['data']);
        $resource->hydrateNestedResources();

        return $resource;
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseUri(): string
    {
        $this->confirmConfigured();

        $domain = str($this->apiBase)->replaceFirst("//", "//{$this->subdomain}.");

        return "$domain/api/{$this->version}/";
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function confirmConfigured(): static
    {
        if (! isset($this->version, $this->subdomain, $this->apiKey, $this->apiBase)) {
            throw new IncompleteConfigurationException();
        }

        return $this;
    }

    public function httpClient(): HttpClient
    {
        return $this->httpClient;
    }

    public static function configure(string $apiKey, string $subdomain, string $version = null, string $apiBase = null): static
    {
        self::$singleton = new static;

        return self::$singleton
            ->setApiKey($apiKey)
            ->setSubdomain($subdomain)
            ->setVersion($version ?? self::$singleton->version)
            ->setApiBase($apiBase ?? self::$singleton->apiBase);
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