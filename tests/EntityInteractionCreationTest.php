<?php

namespace MonkeyPod\Api\Tests;

use Illuminate\Support\Str;
use MonkeyPod\Api\Exception\UnmetDependencyException;
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntityInteraction;

class EntityInteractionCreationTest extends TestCase
{
    public function setUp(): void
    {
        $this->configureDummyClient();
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

    public function testThrowsExceptionWhenNoEntityIsPresent()
    {
        $interaction = new EntityInteraction();

        $this->expectException(UnmetDependencyException::class);
        $interaction->getBaseEndpoint();
    }

    public function testThrowsExceptionWhenEntityIsPresentButMissingId()
    {
        $entity = new Entity();
        $interaction = $entity->interaction();

        $this->expectException(UnmetDependencyException::class);
        $interaction->getBaseEndpoint();
    }

    public function testAttachesEntityAfterConstruction()
    {
        $entityId = Str::uuid()->toString();
        $entity = new Entity($entityId);

        $interaction = new EntityInteraction();
        $interaction->attachToEntity($entity);
        $endpoint = $interaction->getBaseEndpoint();

        $this->assertStringContainsString($entityId, $endpoint);
    }
}
