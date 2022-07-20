<?php

namespace MonkeyPod\Api\Resources\Concerns;

trait ActsAsResource
{
    protected array $data;

    protected function setData($dotpath, $value): static
    {
        data_set($this->data, $dotpath, $value);

        return $this;
    }

    protected function getData($dotpath): mixed
    {
        return data_get($this->data, $dotpath);
    }
}