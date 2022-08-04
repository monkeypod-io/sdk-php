---
layout: default
parent: API Resources
nav_order: 3
---

## Entity Interactions

#### Create an Entity Interaction

```php 
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntityInteraction;

/**
 * Because an interaction cannot exist without an associated entity
 * it should be instantiated through the static forEntity() method,
 * which accepts an entity resource object:
 */
$interaction = EntityInteraction::forEntity(new Entity($entityId))

// Optionally, pass a second ID to serve as the ID for the interaction itself:

$interaction = EntityInteraction::forEntity(new Entity($entityId), $interactionId)

// Alternatively, you can use the interaction() method on the resource object:

$entity = new Entity($entityId);
$interaction = $entity->interaction($interactionId); // $interactionId is optional here as above

// Some fields you can set:

$interaction->type = "Phone Call";
$interaction->description = "She called to ask whether we had any tables left for the gala. I told her we were sold out.";
$interaction->link = "https://some-relevant-link-with.more/info-about-the/interaction";
$interaction->happened_at = "2022-06-15 11:42am";

/** 
 * Optionally flag the interaction as "important". Acceptable values are 1, 2, or 3,
 * corresponding to negative, neutral, or positive. Or leave it null to ignore.
 */
$interaction->flag = 3;  

// Now beam it up to the mothership!

$interaction->create();
```

#### Retrieve a Collection of Interactions for a Specific Entity

```php 
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntityInteraction;
use MonkeyPod\Api\Resources\EntityInteractionCollection;

// Because interactions cannot exist without an associated entity
// the collection can be instantiated through the static forEntity() 
// method, which accepts an entity resource object:

$interactions = EntityInteractionCollection::forEntity(new Entity($entityId))

// Alternatively, you can use the interactions() method on the resource object:

$entity = new Entity($entityId);
$interactions = $entity->interactions();

// In either case, to retrieve records from the API:

$interactions->retrieve();
```
