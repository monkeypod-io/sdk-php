<?php

namespace MonkeyPod\Api\Tests;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntityInteraction;

class EntityInteractionCreationTest extends TestCase
{
    public function setUp(): void
    {
        Client::configure("fake-api-key", "fake-subdomain");
    }

    public function testConstruction()
    {
        $entityId = Str::uuid()->toString();
        $entity = new Entity($entityId);

        $interactionId = Str::uuid()->toString();
        $interaction = EntityInteraction::forEntity($entity, $interactionId);

        $this->assertInstanceOf(EntityInteraction::class, $interaction);
        $this->assertEquals($interactionId, $interaction->id);
        $this->assertEquals(
            "https://fake-subdomain.monkeypod.io/api/v2/entities/$entityId/interactions",
            $interaction->getBaseEndpoint()
        );
    }

    public function testConstructionFromEntityMethod()
    {
        $entityId = Str::uuid()->toString();
        $entity = new Entity($entityId);

        $interaction = $entity->interaction();

        $this->assertInstanceOf(EntityInteraction::class, $interaction);
        $this->assertEquals(
            "https://fake-subdomain.monkeypod.io/api/v2/entities/$entityId/interactions",
            $interaction->getBaseEndpoint()
        );
    }
}
