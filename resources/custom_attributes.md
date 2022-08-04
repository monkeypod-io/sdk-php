---
layout: default
parent: API Resources
nav_order: 2
---

## Custom Attributes

### Retrieve a Collection of Custom Attributes
```php 
use MonkeyPod\Api\Resources\EntityAttribute;
use MonkeyPod\Api\Resources\EntityAttributeCollection;

$attributes = new EntityAttributeCollection();
$attributes->retrieve();

foreach ($attributes as $attribute) {
    $attribute instanceof EntityAttribute; // true
}

while ($attributes->currentPage < $attributes->lastPage) {
    $attributes->retrieve($attributes->currentPage + 1);
}

// or

$attributes = new EntityAttributeCollection();
$attributes->retrieve();

foreach ($attributes->autoPagingIterator() as $attribute) {
    // magically fetches all attributes for all pages!
}

/**
 * The most important field on an attribute resource is generally "slug",
 * since that's what you'll use when populating the "extra_attributes" 
 * property on an Entity resource.
 */
$slug = $attribute->slug;
$entity->$slug = "Important custom data!"
```
