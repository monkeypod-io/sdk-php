<?php

namespace MonkeyPod\Api\Resources\Concerns;

use Illuminate\Http\Client\Response;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidResourceException;
use MonkeyPod\Api\Resources\Contracts\Resource;

trait ActsAsResource
{
    protected array $data = [];

    public function __construct(string $id = null)
    {
        if ($id) {
            $this->set("id", $id);
        }
    }

    /**
     * @throws IncompleteConfigurationException
     * @throws ApiResponseError
     */
    public function create(array $data = null): static
    {
        $endpoint = $this->getBaseEndpoint();
        $data ??= $this->data;

        $this->data = Client::singleton()->post($endpoint, $data)['data'];
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

        $this->data = Client::singleton()->get($endpoint)['data'];
        $this->hydrateNestedResources();

        return $this;
    }

    public function set($dotpath, $value): static
    {
        if (null === $dotpath) {
            $this->data = $value;
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
}