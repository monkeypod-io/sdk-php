<?php

namespace MonkeyPod\Api;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidRequestException;
use MonkeyPod\Api\Exception\InvalidResourceException;
use MonkeyPod\Api\Exception\ResourceNotFoundException;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Entity;

class Client
{
    protected string $version = "v2";

    protected string $subdomain;

    protected string $apiKey;

    protected string $apiHost = "https://monkeypod.io";

    protected HttpClient $httpClient;

    protected bool $verifySsl = true;

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

    public function setApiHost(string $url): static
    {
        $this->apiHost = $url;

        return $this;
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseUri(): string
    {
        $this->confirmConfigured();

        $domain = str($this->apiHost)->replaceFirst("//", "//{$this->subdomain}.");

        return "$domain/api/{$this->version}/";
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function confirmConfigured(): static
    {
        if (! isset($this->version, $this->subdomain, $this->apiKey, $this->apiHost)) {
            throw new IncompleteConfigurationException();
        }

        return $this;
    }

    public function httpClient(): HttpClient
    {
        return $this->httpClient;
    }

    /**
     * @throws ApiResponseError
     */
    public function get($endpoint): ?array
    {
        return $this->httpClient
            ->withToken($this->apiKey)
            ->acceptJson()
            ->withOptions(['verify' => $this->verifySsl])
            ->get($endpoint)
            ->onError(function (Response $response) {
                throw match ($response->status()) {
                    404 => new ResourceNotFoundException(),
                    default => (new ApiResponseError())
                        ->setHttpStatus($response->status())
                        ->setErrors($response->json("errors", []))
                };
            })
            ->json();
    }

    /**
     * @throws ApiResponseError
     */
    public function post($endpoint, array $data): ?array
    {
        return $this->httpClient
            ->withToken($this->apiKey)
            ->acceptJson()
            ->withOptions(['verify' => $this->verifySsl])
            ->post($endpoint, $data)
            ->onError(function (Response $response) {
                throw match ($response->status()) {
                    404 => new ResourceNotFoundException(),
                    422 => (new InvalidRequestException())->setErrors($response->json("errors", [])),
                    default => (new ApiResponseError())->setHttpStatus($response->status()),
                };
            })
            ->json();
    }

    public static function configure(string $apiKey, string $subdomain, string $version = null, string $apiBase = null): static
    {
        self::$singleton = new static;

        return self::$singleton
            ->setApiKey($apiKey)
            ->setSubdomain($subdomain)
            ->setVersion($version ?? self::$singleton->version)
            ->setApiHost($apiBase ?? self::$singleton->apiHost);
    }

    public function verifySsl(bool $verify = true): static
    {
        $this->verifySsl = $verify;

        return $this;
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