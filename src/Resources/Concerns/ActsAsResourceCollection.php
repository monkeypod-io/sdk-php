<?php

namespace MonkeyPod\Api\Resources\Concerns;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @implements  ActsAsResourceCollection
 */
trait ActsAsResourceCollection
{
    public ?int $page = null;

    public ?string $firstPageUrl = null;

    public ?string $prevPageUrl = null;

    public ?string $nextPageUrl = null;

    protected Collection $resources;

    public function __construct(
        protected ?Client $client = null,
    )
    {
        $this->client ??= Client::singleton();

        $this->resources = collect();
    }

    public function current(): Resource
    {
        return current($this->resources);
    }

    /**
     * @throws ApiResponseError
     */
    public function next(): void
    {
        if (current($this->resources) !== $this->resources->last() || ! $this->nextPageUrl) {
            next($this->resources);
            return;
        }

        $this->loadUri($this->nextPageUrl);
    }

    public function key(): string
    {
        return key($this->resources);
    }

    public function valid(): bool
    {
        return (bool) current($this->resources);
    }

    /**
     * @throws ApiResponseError
     */
    public function rewind(): void
    {
        if (! $this->page || 1 === $this->page) {
            rewind($this->resources);
            return;
        }

        $this->loadUri($this->firstPageUrl);
    }

    public function offsetExists(mixed $offset): bool
    {
        return $this->resources->has($offset);
    }

    public function offsetGet(mixed $offset): ?Resource
    {
        return $this->resources->get($offset);
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new \BadMethodCallException("Collection elements may not be set or unset directly");
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new \BadMethodCallException("Collection elements may not be set or unset directly");
    }

    /**
     * @throws ApiResponseError
     * @throws IncompleteConfigurationException
     */
    public function load(int $page = 1): void
    {
        $base = $this->getBaseEndpoint();
        $baseComponents = parse_url($base);
        $queryString = Arr::get($baseComponents, 'query', '?');
        parse_str($queryString, $queryParams);

        $queryParams['page'] = $page;
        $modifiedQueryString = http_build_query($queryParams);

        $endpoint = str($base)
            ->replace($queryString, "")
            ->append($modifiedQueryString)
            ->toString();

        $this->loadUri($endpoint);
    }

    /**
     * @throws ApiResponseError
     */
    protected function loadUri(string $uri): void
    {
        $response = $this->client->get($uri);

        $this->page = $response['current_page'];
        $this->firstPageUrl = $response['first_page_url'];
        $this->nextPageUrl = $response['next_page_url'];
        $this->prevPageUrl = $response['prev_page_url'];

        $this->resources = collect($response['data'])
            ->mapWithKeys(function ($resourceData) {
                $resource = $this->buildResource($resourceData);
                return [$resource->id => $resource];
            });
    }

    abstract protected function buildResource(array $data): Resource;
}