<?php

namespace MonkeyPod\Api\Exception;

class InvalidResourceException extends \Exception
{
    public $message = "The provided resource is invalid";
}