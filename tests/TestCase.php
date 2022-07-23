<?php

namespace MonkeyPod\Api\Tests;

use MonkeyPod\Api\Client;

class TestCase extends \PHPUnit\Framework\TestCase
{
    public function tearDown(): void
    {
        Client::forgetSingleton();
        parent::tearDown();
    }

    protected function configureDummyClient(): Client
    {
        return Client::configure("not-a-real-api-key", "fake-subdomain");
    }
}