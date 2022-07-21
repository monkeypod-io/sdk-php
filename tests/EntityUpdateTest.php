<?php

namespace MonkeyPod\Api\Tests;

use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntityPhone;

class EntityUpdateTest extends TestCase
{
    /**
     * @noinspection PhpUnhandledExceptionInspection
     */
    public function testMergesDataInUpdateRequest()
    {
        $responseData = json_decode(file_get_contents(__DIR__ . "/json/entity.json"), true);
        $uuid = $responseData['id'];

        Client::configure("fake-api-key", "fake-subdomain")
            ->httpClient()
            ->preventStrayRequests()
            ->fake([
                "fake-subdomain.monkeypod.io/api/v2/entities/$uuid" => (new Factory())->response($responseData),
            ]);

        $random1 = Str::random();
        $random2 = Str::random();

        $entity = new Entity($uuid);
        $entity->first_name = $random1;
        $entity->update([
            "last_name" => $random2
        ]);

        Client::singleton()->httpClient()->assertSent(fn (Request $request) =>
            $request->data() === [
                "id" => $uuid,
                "first_name" => $random1,
                "last_name" => $random2
            ] &&
            $request->url() === "https://fake-subdomain.monkeypod.io/api/v2/entities/$uuid" &&
            $request->method() === "PUT"
        );
    }
}