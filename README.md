# MonkeyPod API - PHP SDK
Official PHP libraries for communicating with the MonkeyPod API. Documentation
for the API can be found in the 
[MonkeyPod Knowledgebase](https://monkeypod.helpscoutdocs.com/category/134-api?sort=).

## Releases / Stability
Releases prior to 1.0 should be considered unstable and make change at any time. If 
you are using these libraries in production, make sure you have good test coverage and
confirm that everything still works after any upgrades.

## Installation
```composer require monkeypod/api```

## Setup and Configuration

Consider running your setup and configuration code somewhere in your application's boot process.
The two pieces of data you'll need are:
1. Your API Key
2. The subdomain where your organization accesses MonkeyPod
(e.g., "goodwork" for "goodwork.monkeypod.io")

```php

use MonkeyPod\Api\Client;

$apiKey = "your-api-key-from-monkeypod";
$subdomain = "my-organization-subdomain";

// Static configuration creates a global Client object that
// will automatically be used for all API calls.

Client::configure($apiKey, $subdomain);

// Alternatively, dynamic configuration scopes the configuration
// to the specific client object. 

$client = new Client();
$client->setApiKey($apiKey);
$client->setSubdomain($subdomain);

// If you use this approach, you'll need to pass the client in
// the constructor of any resources.

$entity = new \MonkeyPod\Api\Resources\Entity($client);
```

## Laravel
This library uses a few components from the [Laravel](https://laravel.com) framework, 
but it can be used with or without Laravel. If you do use Laravel, there is a test 
helper that allows you to make API calls using Laravel's test HTTP client. This could 
be useful if you need to mock the MonkeyPod API server locally.

# API Resources
Not all MonkeyPod data is available in the API, and not all actions are 
available for each resource. As new resources are surfaced in the API
we will do our best to keep the SDK current.

### Constructors
Most resource constructors can accept the following parameters, in any order:
* a Client object to use for API calls
* a resource ID (must be a properly-formatted UUID)



### Required, optional, and additional/unlisted fields
Required fields are labeled in this documentation. Other fields are optional. 
Not all available fields are shown in these docs. For a current list of all
fields that apply to each resource, consult the 
[API documentation](https://monkeypod.helpscoutdocs.com/category/134-api?sort=).

# API Resource Collections
Resource collection classes implement the ResourceCollection interface and
are created when you specifically request a group of records from the API.

#### Pagination
By default, a resource collection will contain (up to) the first 15 results
from the API call. However, you may use the ```autoPagingIterator()``` method
to return an iterator that invisibly requests additional pages of results
until the last page is reached. 

# List of API Resources and Actions

## Entities (a.k.a. "Relationships")

**Note:** MonkeyPod's API uses the term "entity" to refer to what MonkeyPod itself calls
a "relationship". This is to avoid confusion in a context where the word "relationship" can
have a different, technical meaning (i.e. when a database record is connected to one or 
more other records). 

#### Create a New Entity

```php 
use MonkeyPod\Api\Resources\Entity;

$person = new Entity;
$person->type = "Individual";               // REQUIRED, one of "Individual", "Organization", "Foundation", "Corporate", "Government", or "Other"
$person->first_name = "Jane";
$person->last_name = "Smith";               // REQUIRED, only when type is "Individual" and email is not provided
$person->organization_name = "Acme, Inc.";  // REQUIRED, only when type is not "Individual"
$person->email = "jane.smith@example.com";  // REQUIRED, only when type is "Individual" and last_name is not provided
...
$person->create();

/** 
 * If you didn't supply an ID, one will be assigned by MonkeyPod, 
 * which you probably want to store for future reference (for example, 
 * to associate it with a data record in your application that corresponds
 * or relates to the created entity).
 */
$entity->id;
```

#### Retrieve an Entity
```php 
use MonkeyPod\Api\Resources\Entity;

$idFromPreviousApiCall = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$person = new Entity($idFromPreviousApiCall);
$person->retrieve();

$person->first_name; // Jane
```

#### Update an Entity

```php 
use MonkeyPod\Api\Resources\Entity;

$id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";

// The Entity object must know the ID of the API resource 

$person = new Entity($id);

// Update requests combine all populated data with any additional data 
// that is included in the update() call. 

$person->first_name = "John";
$person->update([
    'last_name' => 'Jones'
]);

$nowNamedJohn = new Entity($id);
$nowNamedJohn->retrieve();
$nowNamedJohn->first_name; // John
$nowNamedJohn->last_name;  // Jones
```

#### Delete (or Deactivate) an Entity

```php 
use MonkeyPod\Api\Resources\Entity;

$id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$person = new Entity($id);
$person->delete();

// An entity will only be deleted in MonkeyPod if that can be done 
// without violating data integrity. If the entity has associated 
// transactions or other data, it will be deactivated instead.  
```

#### Match Existing Entities
```php
use MonkeyPod\Api\Resources\Entity;

$comparator = new Entity();
$comparator->email = 'jane@example.com';

$matches = new \MonkeyPod\Api\Resources\EntityCollection();
$matches->match($comparator);

// API will also match on ID or name
$comparator = new Entity();
$comparator->first_name = 'Jane';
$comparator->last_name = 'Smith';

$matches = new \MonkeyPod\Api\Resources\EntityCollection();
$matches->match($comparator);

// Possible matches will be populated to the collection 
// with the strongest candidates first.
```

## Custom Attributes

#### Retrieve a Collection of Custom Attributes

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

// The most important field on an attribute resource is generally "slug",
// since that's what you'll use when populating the "extra_attributes" 
// property on an Entity resource.

$slug = $attribute->slug;
$entity->$slug = "Important custom data!"
```

## Entity Interactions

#### Create an Entity Interaction

```php 
use MonkeyPod\Api\Resources\Entity;
use MonkeyPod\Api\Resources\EntityInteraction;

// Because an interaction cannot exist without an associated entity
// it should be instantiated through the static forEntity() method,
// which accepts an entity resource object:

$interaction = EntityInteraction::forEntity(new Entity($entityId))

// Optionally, pass a second ID to serve as the ID for the interaction itself:

$interaction = EntityInteraction::forEntity(new Entity($entityId), $interactionId)

// Alternatively, you can use the interaction() method on the resource object:

$entity = new Entity($entityId);
$interaction = $entity->interaction($interactionId); // $interactionId is optional here as above

// Some fields you can set:

$interaction->type = "Phone Call"; // REQUIRED
$interaction->description = "She called to ask whether we had any tables left for the gala. I told her we were sold out."; // REQUIRED
$interaction->link = "https://some-relevant-link-with.more/info-about-the/interaction";
$interaction->happened_at = "2022-06-15 11:42am"; // REQUIRED, a valid datetime string

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

## Entity Phones

#### Retrieve a Collection of Phone Numbers for a Specific Entity

COMING SOON.

#### Retrieve a Single Phone Number

COMING SOON.

#### Delete a Phone Number

COMING SOON.

## Future Development

##### Other Resources / Endpoints to Consider
* Accounts
  * Retrieve account
  * Retrieve collection of accounts
* Classes
  * Retrieve class
  * Retrieve collection of classes
* Donation
  * Retrieve a donation
  * Create a donation
  * Update a donation
  * Delete a donation
* Entity
  * Populate from webhook payload
* Items
  * Retrieve item
  * Retrieve collection of items
* Pipelines
  * Add relationship to pipeline
  * Update a relationship's status for a pipeline step
* Sales
  * Retrieve a sale
  * Create a sale
  * Update a sale
  * Delete a sale
* Tags
  * Retrieve tag
  * Retrieve collection of tags
    * All tags
    * By type
* Webhooks
  * Retrieve a collection of webhooks
  * Create a webhook
  * Delete a webhook
* Events
  * Retrieve a collection of webhook-capable events
