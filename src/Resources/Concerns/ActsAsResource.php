<?php

namespace MonkeyPod\Api\Resources\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MonkeyPod\Api\Attributes\AccessibleProperty;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Contracts\Resource;
use phpDocumentor\Reflection\DocBlock\Tags\Property;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyRead;
use phpDocumentor\Reflection\DocBlock\Tags\PropertyWrite;
use phpDocumentor\Reflection\DocBlockFactory;

/**
 * @property string $id
 */
trait ActsAsResource
{
    protected array $data = [];

    protected array $links = [];

    protected Client $apiClient;

    protected ?string $idempotencyKey = null;

    protected static array $accessibleProperties;

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
     * Create a new record from the resource
     *
     * @throws IncompleteConfigurationException
     * @throws ApiResponseError
     */
    public function create(array $data = []): static
    {
        $endpoint = $this->getBaseEndpoint();
        $data = array_merge_recursive($this->data, $data);

        $headers = isset($this->idempotencyKey)
            ? ['X-MonkeyPod-IdempotencyKey' => $this->idempotencyKey]
            : [];

        $response = $this->apiClient->post($endpoint, $data, $headers);

        $this->data = $response['data'];
        $this->links = $response['links'] ?? [];

        $this->hydrateNestedResources();

        return $this;
    }

    /**
     * Retrieve resource data from the API
     *
     * @throws IncompleteConfigurationException
     * @throws ApiResponseError
     */
    public function retrieve(): static
    {
        $endpoint = $this->getSpecificEndpoint();
        $response = $this->apiClient->get($endpoint);

        $this->data = $response['data'];
        $this->links = $response['links'] ?? [];

        $this->hydrateNestedResources();

        return $this;
    }

    /**
     * Update a record from the resource's updated properties
     *
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
     * Delete (or deactivate) a record corresponding to the resource
     *
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
            $id = $this->data['id'] ?? $value['id'] ?? null;
            $this->data = $value;
            if (isset($id)) {
                $this->data['id'] = $id;
            }

        } else {
            data_set($this->data, $dotpath, $value);
        }

        return $this;
    }

    public function get($dotpath = null): mixed
    {
        return data_get($this->data, $dotpath);
    }

    /**
     * Get a URL from the metadata links returned from the API
     *
     * @param string $ref
     * @return string|null
     */
    public function getLink(string $ref): ?string
    {
        return Arr::get($this->links, $ref);
    }

    public function __get(string $name): mixed
    {
        return $this->get($name);
    }

    public function __set(string $name, mixed $value): void
    {
        $this->set($name, $value);
    }

    public function __call(string $method, array $arguments = []): mixed
    {
        if (! str_starts_with($method, "set") && ! str_starts_with($method, "get")) {
            throw new \BadMethodCallException("Method $method does not exist");
        }

        $property = substr($method, 3);
        $documentedProperties = static::getAccessibleProperties();

        if (! array_key_exists($property, $documentedProperties)) {
            throw new \InvalidArgumentException("Failed to handle getter/setter $method");
        }

        return str_starts_with($method, "set")
            ? $this->set($documentedProperties[$property], $arguments[0])
            : $this->get($documentedProperties[$property]);
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

    public function fill(array $properties): static
    {
        foreach ($properties as $key => $value) {
            $this->$key = $value;
        }

        return $this;
    }

    public function idempotencyKey(?string $key): static
    {
        $this->idempotencyKey = $key;

        return $this;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * @return array    A key-value dictionary of getter/setter methods and corresponding data keys
     */
    static protected function getAccessibleProperties(): array
    {
        if (! isset(static::$accessibleProperties)) {
            // First get the phpdoc documented properties
            $reflection = new \ReflectionClass(static::class);
            $docblock = DocBlockFactory::createInstance()->create($reflection->getDocComment());
            static::$accessibleProperties = collect()
                ->merge($docblock->getTagsByName("property"))
                ->merge($docblock->getTagsByName("property-read"))
                ->merge($docblock->getTagsByName("property-write"))
                ->mapWithKeys(
                    fn ($property) => [Str::studly($property->getVariableName()) => $property->getVariableName()]
                )
                ->toArray();

            // Then get the nested stuff that's defined by attributes
            collect($reflection->getAttributes(AccessibleProperty::class))
                ->each(function (\ReflectionAttribute $attribute) {
                    /** @var AccessibleProperty $attributeInstance */
                    $attributeInstance = $attribute->newInstance();
                    static::$accessibleProperties[$attributeInstance->accessorMutator] = $attributeInstance->key;
                });
        }

        return static::$accessibleProperties;
    }
}