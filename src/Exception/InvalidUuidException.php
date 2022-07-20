<?php

namespace MonkeyPod\Api\Exception;

class InvalidUuidException extends \Exception
{
    public $message = "The provided value is not a valid UUID";
}