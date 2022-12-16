---
layout: default
parent: API Resources
nav_order: 4
---

## Email List Subscriptions

### Create an Email List Subscription

```php 
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntitySubscription;

/**
 * Because a subscription cannot exist without an associated entity
 * it should be instantiated through the static forEntity() method,
 * which accepts an entity resource object:
 */
$subscription = EntitySubscription::forEntity(new Entity($entityId))

/**
 * A valid uuid for an email list is required. 
 */
$subscription->email_list_id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";

$subscription->create();
```

Note that if a subscription record already exists (including where the subscriber
has subscribed and then unsubscribed from the list), that will be returned instead 
of creating a new subscription.
