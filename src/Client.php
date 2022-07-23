<?php

namespace MonkeyPod\Api;

use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidRequestException;
use MonkeyPod\Api\Exception\ResourceNotFoundException;

class Client
{
    protected string $version = "v2";

    protected string $subdomain;

    protected string $apiKey;

    protected string $apiHost = "https://monkeypod.io";

    protected HttpClient $httpClient;

    protected bool $verifySsl = true;

    protected bool $testMode = false;

    /**
     * @var Illuminate\Foundation\Testing\TestCase
     * @noinspection PhpUndefinedNamespaceInspection
     * @noinspection PhpUndefinedClassInspection
     */
    private $laravelTestCase;
    
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
        if ($this->testMode) {
            return $this->getTest($endpoint);
        }

        $response = $this->httpClient
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
            });

        return $response->json();
    }

    /**
     * @throws ApiResponseError
     */
    public function post($endpoint, array $data): ?array
    {
        if ($this->testMode) {
            return $this->postTest($endpoint, $data);
        }

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

    /**
     * @throws ApiResponseError
     */
    public function put($endpoint, array $data): ?array
    {
        if ($this->testMode) {
            return $this->putTest($endpoint, $data);
        }

        return $this->httpClient
            ->withToken($this->apiKey)
            ->acceptJson()
            ->withOptions(['verify' => $this->verifySsl])
            ->put($endpoint, $data)
            ->onError(function (Response $response) {
                throw match ($response->status()) {
                    404 => new ResourceNotFoundException(),
                    422 => (new InvalidRequestException())->setErrors($response->json("errors", [])),
                    default => (new ApiResponseError())->setHttpStatus($response->status()),
                };
            })
            ->json();
    }

    /**
     * @throws ApiResponseError
     */
    public function delete($endpoint): bool
    {
        if ($this->testMode) {
            return $this->deleteTest($endpoint);
        }

        $this->httpClient
            ->withToken($this->apiKey)
            ->acceptJson()
            ->withOptions(['verify' => $this->verifySsl])
            ->delete($endpoint)
            ->onError(function (Response $response) {
                throw match ($response->status()) {
                    404 => new ResourceNotFoundException(),
                    422 => (new InvalidRequestException())->setErrors($response->json("errors", [])),
                    default => (new ApiResponseError())->setHttpStatus($response->status()),
                };
            });

        return true;
    }

    public static function configure(string $apiKey, string $subdomain, string $version = null, string $apiHost = null): static
    {
        self::$singleton = new static;

        return self::$singleton
            ->setApiKey($apiKey)
            ->setSubdomain($subdomain)
            ->setVersion($version ?? self::$singleton->version)
            ->setApiHost($apiHost ?? self::$singleton->apiHost);
    }

    public function verifySsl(bool $verify = true): static
    {
        $this->verifySsl = $verify;

        return $this;
    }

    /**
     * Can be used to substitute a Laravel test case
     * instead of the real Http client.
     * 
     * @param Illuminate\Foundation\Testing\TestCase $testCase
     * @return $this
     * @noinspection PhpUndefinedNamespaceInspection
     * @noinspection PhpUndefinedClassInspection
     */
    public function withLaravelTestCase($testCase): static
    {
        $this->testMode = true;
        $this->verifySsl = false;
        $this->testClient = $testCase;

        return $this;
    }

    protected function postTest($endpoint, array $data): ?array
    {
        $response = $this->testClient
            ->withToken($this->apiKey)
            ->postJson($endpoint, $data);

        if ($response->isSuccessful()) {
            return $response->json();
        }

        throw match ($response->status()) {
            404 => new ResourceNotFoundException(),
            422 => (new InvalidRequestException())->setErrors($response->json("errors", [])),
            default => (new ApiResponseError())->setHttpStatus($response->status()),
        };
    }

    protected function putTest($endpoint, array $data): ?array
    {
        $response = $this->testClient
            ->withToken($this->apiKey)
            ->putJson($endpoint, $data);

        if ($response->isSuccessful()) {
            return $response->json();
        }

        throw match ($response->status()) {
            404 => new ResourceNotFoundException(),
            422 => (new InvalidRequestException())->setErrors($response->json("errors", [])),
            default => (new ApiResponseError())->setHttpStatus($response->status()),
        };
    }

    protected function deleteTest($endpoint): bool
    {
        $response = $this->testClient
            ->withToken($this->apiKey)
            ->deleteJson($endpoint);

        if ($response->isSuccessful()) {
            return true;
        }

        throw match ($response->status()) {
            404 => new ResourceNotFoundException(),
            422 => (new InvalidRequestException())->setErrors($response->json("errors", [])),
            default => (new ApiResponseError())->setHttpStatus($response->status()),
        };
    }

    protected function getTest($endpoint): ?array
    {
        return $this->testClient
            ->withToken($this->apiKey)
            ->getJson($endpoint)
            ->json();
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

    public static function forgetSingleton(): void
    {
        self::$singleton = new static;
    }
}