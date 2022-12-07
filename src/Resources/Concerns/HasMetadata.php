<?php

namespace MonkeyPod\Api\Resources\Concerns;

use Illuminate\Support\Arr;

trait HasMetadata
{
    public function setMetadata(array | string $mixed, $data = null): static
    {
        if (! isset($this->data['metadata'])) {
            $this->set('metadata', []);
        }

        if (is_array($mixed)) {
            foreach ($mixed as $key => $value) {
                Arr::set($this->data['metadata'], $key, $value);
            }
        } else {
            Arr::set($this->data['metadata'], $mixed, $data);
        }

        return $this;
    }

    public function getMetadata(string $key = null, mixed $default = null): mixed
    {
        if (! isset($this->data['metadata'])) {
            $this->set('metadata', []);
        }

        if (null === $key) {
            return $this->data['metadata'];
        }

        return Arr::get($this->data['metadata'], $key, $default);
    }
}