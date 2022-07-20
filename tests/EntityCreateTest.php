<?php

namespace MonkeyPod\Api\Tests;

use Illuminate\Http\Client\Factory;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntityPhone;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class EntityCreateTest extends TestCase
{
    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function testRetrievesAnEntityWithNestedResources()
    {
        $responseData = json_decode(file_get_contents(__DIR__ . "/json/entity.json"), true);

        Client::configure("fake-api-key", "fake-subdomain")
            ->httpClient()
            ->preventStrayRequests()
            ->fake([
                "fake-subdomain.monkeypod.io/api/v2/entities" => (new Factory())->response($responseData),
            ]);

        $data = $responseData["data"];
        unset($data['created_at']);
        unset($data['updated_at']);

        $entity = Entity::create($data);

        $this->assertInstanceOf(Entity::class, $entity);

        $this->assertCount(2, $entity->phones);
        $this->assertInstanceOf(EntityPhone::class, $entity->phones[0]);
        $this->assertInstanceOf(EntityPhone::class, $entity->phones[1]);
        $this->assertNotEmpty($entity->phones[0]->number);
    }
}