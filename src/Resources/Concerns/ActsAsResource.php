<?php

namespace MonkeyPod\Api\Resources\Concerns;

use MonkeyPod\Api\Resources\EntityPhone;

trait ActsAsResource
{
    protected array $data = [];

    public function __construct(string $id = null)
    {
        if ($id) {
            $this->set("id", $id);
        }
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
}