<?php

namespace MonkeyPod\Api\Tests;

use Illuminate\Http\Client\Factory;
use MonkeyPod\Api\Resources\EntityAttribute;
use MonkeyPod\Api\Resources\EntityAttributeCollection;

class EntityAttributeCollectionRetrieveTest extends TestCase
{
    public function testRetrievesCollection()
    {
        $responseData = json_decode(file_get_contents(__DIR__ . '/json/entity_attributes.json'), true);
        $mockResponse = (new Factory())->response($responseData);

        $this
            ->configureDummyClient()
            ->httpClient()
            ->preventStrayRequests()
            ->fake([
                "fake-subdomain.monkeypod.io/api/v2/entity_attributes?page=1" => $mockResponse,
            ]);

        $attributes = new EntityAttributeCollection();
        $attributes->retrieve();

        $this->assertCount(4, $attributes);
        $this->assertEquals(4, $attributes->total);
        foreach ($attributes as $attribute) {
            $this->assertInstanceOf(EntityAttribute::class, $attribute);
        }
    }
}