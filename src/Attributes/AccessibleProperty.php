<?php

namespace MonkeyPod\Api\Attributes;

use \Attribute;

/**
 * This attribute may be associated with a resource class to indicate
 * that particular getter/setting magic methods should point to nested
 * data that can't be reached by naive snake_casing.
 */
#[Attribute(Attribute::IS_REPEATABLE | Attribute::TARGET_CLASS)]
class AccessibleProperty
{
    public function __construct(
        public string $accessorMutator, // e.g., "GiftAmount"
        public ?string $key = null,     // e.g., "gift.amount"
    )
    {}
}