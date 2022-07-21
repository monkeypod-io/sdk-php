<?php

namespace MonkeyPod\Api\Exception;

class ResourceNotFoundException extends ApiResponseError
{
    public $message = "No resource could be found";

    public int $httpStatus = 404;
}