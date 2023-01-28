<?php

namespace MonkeyPod\Api\Resources\Concerns;

use Illuminate\Support\Arr;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Contracts\Resource;
use MonkeyPod\Api\Resources\Contracts\ResourceCollection;

/**
 * @implements  ResourceCollection
 */
trait ActsAsResourceCollection
{
    /**
     * If resources are loaded, the current page number.
     *
     * @var int|null
     */
    public ?int $currentPage = null;

    /**
     * If resources are loaded, the last page number available.
     *
     * @var int|null
     */
    public ?int $lastPage = null;

    /**
     * If resources are loaded, the total number of resources available;
     *
     * @var int|null
     */
    public ?int $total = null;

    /** @var array Array of filters to send as query string parameters when retrieving */
    protected array $filters = [];

    protected ?string $firstPageUrl = null;

    protected ?string $prevPageUrl = null;

    protected ?string $nextPageUrl = null;

    protected array $resources = [];

    public function __construct(
        protected ?Client $apiClient = null,
    )
    {
        $this->apiClient ??= Client::singleton();
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->resources);
    }

    public function count(): int
    {
        return count($this->resources);
    }

    public function first(): ?Resource
    {
        return collect($this->resources)->first();
    }

    /**
     * @throws InvalidUuidException
     * @throws ApiResponseError
     * @throws IncompleteConfigurationException
     */
    public function autoPagingIterator(): \Generator
    {
        while (true) {
            foreach ($this->resources as $resource) {
                yield $resource;
            }

            if (isset($this->nextPageUrl)) {
                $this->retrieveUri($this->nextPageUrl);
            } else {
                break;
            }
        }
    }

    /**
     * @throws ApiResponseError
     * @throws IncompleteConfigurationException
     * @throws InvalidUuidException
     */
    public function retrieve(int $page = 1): static
    {
        $base = $this->getBaseEndpoint();
        $baseComponents = parse_url($base);
        $queryString = Arr::get($baseComponents, 'query', '');
        parse_str($queryString, $queryParams);

        $queryParams['page'] = $page;
        $queryParams += $this->filters;
        $modifiedQueryString = http_build_query($queryParams);

        $endpoint = str($base)
            ->replace($queryString, "")
            ->append("?" . $modifiedQueryString)
            ->replaceFirst("??", "?")
            ->toString();

        $this->retrieveUri($endpoint);

        return $this;
    }

    /**
     * @throws InvalidUuidException
     * @throws IncompleteConfigurationException
     */
    public function fromResponseData(array $response): static
    {
        $this->currentPage = Arr::get($response, 'meta.current_page', 1);
        $this->lastPage = Arr::get($response, 'meta.last_page', 1);
        $this->total = Arr::get($response, 'meta.total', count($response['data']));

        $this->firstPageUrl = Arr::get($response, 'links.first');
        $this->nextPageUrl = Arr::get($response, 'links.next');
        $this->prevPageUrl = Arr::get($response, 'links.prev');

        $this->resources = [];
        foreach ($response['data'] as $item) {
            $this->resources[] = $this->buildResource($item);
        }

        return $this;
    }

    public function shift(): ?Resource
    {
        return array_shift($this->resources);
    }

    public function __call($method, $args)
    {
        if (str_starts_with($method, "with")) {
            $filter = str($method)->replaceFirst("with", "")->snake()->toString();

            return $this->filter($filter, $args[0]);
        }

        throw new \BadMethodCallException("Invalid method $method");
    }

    protected function filter(string $dotpath, mixed $value): static
    {
        Arr::set($this->filters, $dotpath, $value);

        return $this;
    }

    /**
     * @throws ApiResponseError
     * @throws InvalidUuidException
     * @throws IncompleteConfigurationException
     */
    protected function retrieveUri(string $uri): void
    {
        $response = $this->apiClient->get($uri);
        $this->fromResponseData($response);
    }

    abstract protected function buildResource(array $data): Resource;
}