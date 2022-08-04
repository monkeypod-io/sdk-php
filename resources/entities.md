---
layout: default
parent: API Resources
nav_order: 1
---

## Entities (a.k.a. "Relationships")

**Note:** MonkeyPod's API uses the term "entity" to refer to what MonkeyPod itself calls
a "relationship". This is to avoid confusion in a context where the word "relationship" can
have a different, technical meaning (i.e. when a database record is connected to one or 
more other records). It is also useful to distinguish the person/organization itself from 
the totality of your relationship with that person/organization.

#### Create a New Entity

```php 
use MonkeyPod\Api\Resources\Entity;

$person = new Entity;
$person->type = "Individual";
$person->first_name = "Jane";
$person->last_name = "Smith";
$person->organization_name = "Acme, Inc.";
$person->email = "jane.smith@example.com";
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
**Note:** An entity will only be deleted in MonkeyPod if that can be done 
without violating data integrity. If the entity has associated transactions 
or other related data, it will be deactivated instead.  

```php 
use MonkeyPod\Api\Resources\Entity;

$id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$person = new Entity($id);
$person->delete();
```

#### Match Existing Entities
```EntityCollection``` resources can be asked to match against a comparator ```Entity```
resource to find possible matches in your MonkeyPod database. You can attempt to 
match against an ID, an email address, or a first and last name. Possible matches 
will be populated to the collection with the strongest candidates first.

```php
use MonkeyPod\Api\Resources\Entity;

$comparator = new Entity();
$comparator->email = 'jane@example.com';

$matches = new MonkeyPod\Api\Resources\EntityCollection();
$matches->match($comparator);

// or

$comparator = new Entity();
$comparator->first_name = 'Jane';
$comparator->last_name = 'Smith';

$matches = new MonkeyPod\Api\Resources\EntityCollection();
$matches->match($comparator);
```
