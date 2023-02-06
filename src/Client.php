<?php

namespace MonkeyPod\Api;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\Client\Factory as HttpClient;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Event;
use Illuminate\Testing\TestResponse;
use Illuminate\Validation\UnauthorizedException;
use MonkeyPod\Api\Events\ApiCallCompleted;
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
            ->onError(function (Response $response) use ($endpoint) {

                $this->broadcastApiCallCompletedEvent($endpoint, "GET", $response);

                throw match ($response->status()) {
                    404 => new ResourceNotFoundException(),
                    default => $this->makeApiResponseError($response, "GET", $endpoint),
                };
            });

        $this->broadcastApiCallCompletedEvent($endpoint, "GET", $response);

        return $response->json();
    }

    /**
     * @throws ApiResponseError
     */
    public function post($endpoint, array $data, array $headers = []): ?array
    {
        if ($this->testMode) {
            return $this->postTest($endpoint, $data, $headers);
        }

        $response = $this->httpClient
            ->withToken($this->apiKey)
            ->acceptJson()
            ->withHeaders($headers)
            ->withOptions(['verify' => $this->verifySsl])
            ->post($endpoint, $data)
            ->onError(function (Response $response) use ($endpoint) {

                $this->broadcastApiCallCompletedEvent($endpoint, "POST", $response);

                throw match ($response->status()) {
                    401 => new AuthenticationException("Unauthenticated"),
                    403 => new UnauthorizedException("Unauthorized"),
                    404 => new ResourceNotFoundException(),
                    422 => $this->makeInvalidRequestException($response, "POST", $endpoint),
                    default => $this->makeApiResponseError($response, "POST", $endpoint),
                };
            });

        $this->broadcastApiCallCompletedEvent($endpoint, "POST", $response);

        return $response->json();
    }

    /**
     * @throws ApiResponseError
     */
    public function put($endpoint, array $data): ?array
    {
        if ($this->testMode) {
            return $this->putTest($endpoint, $data);
        }

        $response = $this->httpClient
            ->withToken($this->apiKey)
            ->acceptJson()
            ->withOptions(['verify' => $this->verifySsl])
            ->put($endpoint, $data)
            ->onError(function (Response $response) use ($endpoint) {

                $this->broadcastApiCallCompletedEvent($endpoint, "PUT", $response);

                throw match ($response->status()) {
                    404 => new ResourceNotFoundException(),
                    422 => $this->makeInvalidRequestException($response, "PUT", $endpoint),
                    default => $this->makeApiResponseError($response, "PUT", $endpoint),
                };
            });

        $this->broadcastApiCallCompletedEvent($endpoint, "PUT", $response);

        return $response->json();
    }

    /**
     * @throws ApiResponseError
     */
    public function delete($endpoint): bool
    {
        if ($this->testMode) {
            return $this->deleteTest($endpoint);
        }

        $response = $this->httpClient
            ->withToken($this->apiKey)
            ->acceptJson()
            ->withOptions(['verify' => $this->verifySsl])
            ->delete($endpoint)
            ->onError(function (Response $response) use ($endpoint) {

                $this->broadcastApiCallCompletedEvent($endpoint, "DELETE", $response);

                throw match ($response->status()) {
                    403 => new AuthorizationException(),
                    404 => new ResourceNotFoundException(),
                    422 => $this->makeInvalidRequestException($response, "DELETE", $endpoint),
                    default => $this->makeApiResponseError($response, "DELETE", $endpoint),
                };
            });

        $this->broadcastApiCallCompletedEvent($endpoint, "DELETE", $response);

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
     * @param Illuminate\Foundation\Testing\TestCase | false $testCase
     * @return $this
     * @noinspection PhpUndefinedNamespaceInspection
     * @noinspection PhpUndefinedClassInspection
     */
    public function withLaravelTestCase($testCase): static
    {
        if (false === $testCase) {
            $this->testMode = false;
            $this->testClient = null;
        } else {
            $this->testMode = true;
            $this->verifySsl = false;
            $this->testClient = $testCase;
        }

        return $this;
    }

    protected function postTest($endpoint, array $data, array $headers = []): ?array
    {
        $response = $this->testClient
            ->withToken($this->apiKey)
            ->withHeaders($headers)
            ->postJson($endpoint, $data);

        $this->broadcastApiCallCompletedEvent($endpoint, "POST", $response);

        if ($response->isSuccessful()) {
            return $response->json();
        }

        throw match ($response->status()) {
            404 => new ResourceNotFoundException(),
            422 => $this->makeInvalidRequestException($response, "POST", $endpoint),
            default => $this->makeApiResponseError($response, "POST", $endpoint),
        };
    }

    protected function putTest($endpoint, array $data): ?array
    {
        $response = $this->testClient
            ->withToken($this->apiKey)
            ->putJson($endpoint, $data);

        $this->broadcastApiCallCompletedEvent($endpoint, "PUT", $response);

        if ($response->isSuccessful()) {
            return $response->json();
        }

        throw match ($response->status()) {
            404 => new ResourceNotFoundException(),
            422 => $this->makeInvalidRequestException($response, "PUT", $endpoint),
            default => $this->makeApiResponseError($response, "PUT", $endpoint),
        };
    }

    protected function deleteTest($endpoint): bool
    {
        $response = $this->testClient
            ->withToken($this->apiKey)
            ->deleteJson($endpoint);

        $this->broadcastApiCallCompletedEvent($endpoint, "DELETE", $response);

        if ($response->isSuccessful()) {
            return true;
        }

        throw match ($response->status()) {
            403 => new AuthorizationException(),
            404 => new ResourceNotFoundException(),
            422 => $this->makeInvalidRequestException($response, "DELETE", $endpoint),
            default => $this->makeApiResponseError($response, "DELETE", $endpoint),
        };
    }

    protected function getTest($endpoint): ?array
    {
        $response = $this->testClient
            ->withToken($this->apiKey)
            ->getJson($endpoint);

        $this->broadcastApiCallCompletedEvent($endpoint, "GET", $response);

        if ($response->isSuccessful()) {
            return $response->json();
        }

        throw match ($response->status()) {
            401,
            403 => new AuthorizationException(),
            404 => new ResourceNotFoundException(),
            422 => $this->makeInvalidRequestException($response, "GET", $endpoint),
            default => $this->makeApiResponseError($response, "GET", $endpoint),
        };
    }

    protected function broadcastApiCallCompletedEvent($endpoint, $method, $response): void
    {
        try {
            ApiCallCompleted::dispatch($endpoint, $method, $response);
        } catch (BindingResolutionException $e) {
            // Ignore this. Applications that want to use event broadcasting will have this available.
        }
    }

    protected function makeApiResponseError(
        Response | TestResponse $response,
        string $method,
        string $endpoint
    ): ApiResponseError
    {
        return (new ApiResponseError())
            ->setHttpStatus($response->status())
            ->setErrors($response->json("errors") ?? [])
            ->setMethod($method)
            ->setEndpoint($endpoint);
    }

    protected function makeInvalidRequestException(
        Response | TestResponse $response,
        string $method,
        string $endpoint
    ): InvalidRequestException
    {
        return (new InvalidRequestException())
            ->setHttpStatus($response->status())
            ->setErrors($response->json("errors") ?? [])
            ->setMethod($method)
            ->setEndpoint($endpoint);
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