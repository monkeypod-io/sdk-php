<?php

namespace MonkeyPod\Api\Exception;

class IncompleteConfigurationException extends \Exception
{
    public $message = "Incomplete configuration. Make sure version, subdomain, and API key are all set.";
}