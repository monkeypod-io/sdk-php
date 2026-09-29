<?php

namespace MonkeyPod\Api\Tests;

use Illuminate\Http\Client\Factory;
use MonkeyPod\Api\Apps\Memberships\Resources\Membership;
use MonkeyPod\Api\Apps\Memberships\Resources\MembershipCollection;
use MonkeyPod\Api\Resources\Entity;

class MembershipCollectionRetrieveTest extends TestCase
{
    public function testRetrievesAllMemberships()
    {
        $responseData = json_decode(file_get_contents(__DIR__ . '/json/memberships.json'), true);
        $mockResponse = (new Factory())->response($responseData);

        $this
            ->configureDummyClient()
            ->httpClient()
            ->preventStrayRequests()
            ->fake([
                "fake-subdomain.monkeypod.io/api/v2/memberships?page=1" => $mockResponse,
            ]);

        $memberships = new MembershipCollection();
        $memberships->retrieve();

        $this->assertCount(2, $memberships);
        $this->assertEquals(2, $memberships->total);
        foreach ($memberships as $membership) {
            $this->assertInstanceOf(Membership::class, $membership);
        }
    }

    public function testSendsFiltersAsQueryParameters()
    {
        $responseData = json_decode(file_get_contents(__DIR__ . '/json/memberships.json'), true);
        $mockResponse = (new Factory())->response($responseData);

        $this
            ->configureDummyClient()
            ->httpClient()
            ->preventStrayRequests()
            ->fake([
                "fake-subdomain.monkeypod.io/api/v2/memberships?page=1&status=Active&membership_level=abc&paid_through_start=2025-01-01&paid_through_end=2025-12-31" => $mockResponse,
            ]);

        $memberships = (new MembershipCollection())
            ->withStatus('Active')
            ->withMembershipLevel('abc')
            ->withPaidThroughStart('2025-01-01')
            ->withPaidThroughEnd('2025-12-31')
            ->retrieve();

        $this->assertCount(2, $memberships);
    }

    public function testStillRetrievesMembershipsForOneEntity()
    {
        $responseData = json_decode(file_get_contents(__DIR__ . '/json/memberships.json'), true);
        $mockResponse = (new Factory())->response($responseData);

        $this
            ->configureDummyClient()
            ->httpClient()
            ->preventStrayRequests()
            ->fake([
                "fake-subdomain.monkeypod.io/api/v2/entities/8a1b2c3d-4e5f-4a6b-8c7d-9e0f1a2b3c4d/memberships?page=1" => $mockResponse,
            ]);

        $entity = new Entity('8a1b2c3d-4e5f-4a6b-8c7d-9e0f1a2b3c4d');
        $memberships = MembershipCollection::forEntity($entity)->retrieve();

        $this->assertCount(2, $memberships);
    }
}
