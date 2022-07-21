<?php

namespace MonkeyPod\Api\Exception;

class ApiResponseError extends \Exception
{
    public $message = "The API returned an error response";

    public int $httpStatus;

    public array $errors = [];

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
}