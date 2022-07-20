<?php

namespace MonkeyPod\Api\Tests;

use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testDynamicConfiguration()
    {
        $client = new Client();
        $client->setApiKey("foo");
        $client->setVersion("bar");
        $client->setSubdomain("baz");

        $subject = $client->confirmConfigured();

        // No exception thrown
        $this->assertSame($client, $subject);
    }

    public function testStaticConfiguration()
    {
        Client::configure("foo", "baz");

        $singleton = Client::singleton();
        $subject = $singleton->confirmConfigured();

        // No exception thrown
        $this->assertSame($subject, $singleton);
    }

    public function testIncompleteConfiguration()
    {
        $client = new Client();
        $client->setApiKey("foo");

        $this->expectException(IncompleteConfigurationException::class);

        $client->confirmConfigured();
    }
}
