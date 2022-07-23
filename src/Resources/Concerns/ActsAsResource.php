<?php

namespace MonkeyPod\Api\Resources\Concerns;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;

/**
 * @property string $id
 */
trait ActsAsResource
{
    protected array $data = [];

    protected Client $apiClient;

    /**
     * @throws InvalidUuidException
     * @throws IncompleteConfigurationException
     */
    public function __construct(...$parameters)
    {
        foreach ($parameters as $parameter) {
            if ($parameter instanceof Client) {
                $this->apiClient = $parameter;
            }

            if (is_string($parameter)) {
                if (! Str::isUuid($parameter)) {
                    throw new InvalidUuidException();
                }

                $this->set("id", $parameter);
            }
        }

        $this->apiClient ??= Client::singleton();
    }

    /**
     * @throws IncompleteConfigurationException
     * @throws ApiResponseError
     */
    public function create(array $data = null): static
    {
        $endpoint = $this->getBaseEndpoint();
        $data ??= $this->data;

        $this->data = $this->apiClient->post($endpoint, $data)['data'];
        $this->hydrateNestedResources();

        return $this;
    }

    /**
     * @throws IncompleteConfigurationException
     * @throws ApiResponseError
     */
    public function retrieve(): static
    {
        $endpoint = $this->getSpecificEndpoint();

        $this->data = $this->apiClient->get($endpoint)['data'];
        $this->hydrateNestedResources();

        return $this;
    }

    /**
     * @throws IncompleteConfigurationException
     * @throws ApiResponseError
     */
    public function update(array $data = []): static
    {
        $endpoint = $this->getSpecificEndpoint();
        $data = array_merge_recursive($this->data, $data);

        $this->data = $this->apiClient->put($endpoint, $data)['data'];
        $this->hydrateNestedResources();

        return $this;
    }

    /**
     * @throws IncompleteConfigurationException
     * @throws ApiResponseError
     */
    public function delete(): static
    {
        $endpoint = $this->getSpecificEndpoint();

        $this->apiClient->delete($endpoint);

        return $this;
    }

    public function set($dotpath, $value): static
    {
        if (null === $dotpath) {
            // Retain the ID even if we're overwriting everything else
            $id = $this->data['id'] ?? null;
            $this->data = $value;
            $this->data['id'] = $id;

        } else {
            data_set($this->data, $dotpath, $value);
        }

        return $this;
    }

    public function get($dotpath = null): mixed
    {
        return data_get($this->data, $dotpath);
    }

    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    public function hydrateNestedResources(): void
    {
        // Override in resource classes
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getSpecificEndpoint(): string
    {
        return $this->getBaseEndpoint() . "/" . $this->id;
    }

    public function getApiClient(): Client
    {
        return $this->apiClient;
    }
}