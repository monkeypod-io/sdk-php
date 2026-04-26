<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Concerns\HasMetadata;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property    string      $id                 The item's UUID
 * @property    string      $name               The item name
 * @property    ?string     $description        The item description
 * @property    string      $default_price      The default per-unit price (in US dollars)
 * @property    bool        $free               Whether the item is free
 * @property    bool        $active             Whether the item is active in the catalog
 * @property    ?string     $account_id         The UUID of the income account associated with this item
 * @property    ?array      $metadata           Associative array of metadata
 * @property    ?string     $created_at         An ISO 8601 formatted timestamp when the record was created
 * @property    ?string     $updated_at         An ISO 8601 formatted timestamp when the record was last updated
 */
class Item implements Resource
{
    use ActsAsResource;
    use HasMetadata;

    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . 'items';
    }
}
