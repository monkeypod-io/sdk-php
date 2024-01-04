<?php

namespace MonkeyPod\Api\Exception;

class ApiResponseError extends \Exception
{
    public $message = "The API returned an error response";

    public int $httpStatus;

    public array $errors = [];

    public string $endpoint;

    public string $method;

    public function setEndpoint($endpoint): static
    {
        $this->endpoint = $endpoint;
        
        return $this;
    }
    
    public function setMethod($method): static
    {
        $this->method = $method;
        
        return $this;
    }

    public function setHttpStatus($status): static
    {
        $this->httpStatus = $status;

        return $this;
    }

    public function setErrors(array $errors): static
    {
        $this->errors = $errors;

        return $this;
    }
    
    public function errors(string $key = null): array
    {
        if (is_null($key)) {
            return $this->errors;
        }
        
        return $this->errors[$key] ?? [];
    }
}