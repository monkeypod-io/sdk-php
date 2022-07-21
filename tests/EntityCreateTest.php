<?php

namespace MonkeyPod\Api\Tests;

use Illuminate\Http\Client\Factory;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntityPhone;

class EntityCreateTest extends TestCase
{
    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function testCreatesStaticallyWithNestedResources()
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

        $entity = new Entity;
        $entity->create($data);

        $this->assertCount(2, $entity->phones);
        $this->assertInstanceOf(EntityPhone::class, $entity->phones[0]);
        $this->assertInstanceOf(EntityPhone::class, $entity->phones[1]);
        $this->assertNotEmpty($entity->phones[0]->number);
    }

    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function testCreatesDynamicallyWithNestedResources()
    {
        $responseData = json_decode(file_get_contents(__DIR__ . "/json/entity.json"), true);

        $client = new Client();
        $client
            ->setApiKey("fake-api-key")
            ->setSubdomain("fake-subdomain");

        $client
            ->httpClient()
            ->preventStrayRequests()
            ->fake([
                "fake-subdomain.monkeypod.io/api/v2/entities" => (new Factory())->response($responseData),
            ]);

        $data = $responseData["data"];
        unset($data['created_at']);
        unset($data['updated_at']);

        $entity = new Entity($client);
        foreach ($data as $key => $value) {
            $entity->$key = $value;
        }
        $entity->create();

        $this->assertCount(2, $entity->phones);
        $this->assertInstanceOf(EntityPhone::class, $entity->phones[0]);
        $this->assertInstanceOf(EntityPhone::class, $entity->phones[1]);
        $this->assertNotEmpty($entity->phones[0]->number);
    }
}