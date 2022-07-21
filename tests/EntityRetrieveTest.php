<?php

namespace MonkeyPod\Api\Tests;

use Illuminate\Http\Client\Factory;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntityPhone;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class EntityRetrieveTest extends TestCase
{
    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function testRetrievesAnEntityWithNestedResources()
    {
        $uuid = Uuid::uuid4()->toString();

        $responseData = json_decode(file_get_contents(__DIR__ . "/json/entity.json"), true);

        Client::configure("fake-api-key", "fake-subdomain")
            ->httpClient()
            ->preventStrayRequests()
            ->fake([
                "fake-subdomain.monkeypod.io/api/v2/entities/$uuid" => (new Factory())->response($responseData),
            ]);

        $entity = new Entity($uuid);
        $entity->retrieve();

        $this->assertCount(2, $entity->phones);
        $this->assertInstanceOf(EntityPhone::class, $entity->phones[0]);
        $this->assertInstanceOf(EntityPhone::class, $entity->phones[1]);
        $this->assertNotEmpty($entity->phones[0]->number);
    }
}