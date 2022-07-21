<?php

namespace MonkeyPod\Api\Exception;

class InvalidRequestException extends ApiResponseError
{
    public $message = "The data supplied was invalid";

    public int $httpStatus = 422;
}