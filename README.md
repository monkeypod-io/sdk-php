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
Resource constructors can accept the following parameters, in any order:
* a Client object to use for API calls
* a resource ID (must be a properly-formatted UUID)

### Required, optional, and additional/unlisted fields
Required fields are labeled in this documentation. Other fields are optional. 
Not all available fields are shown in these docs. For a current list of all
fields that apply to each resource, consult the 
[API documentation](https://monkeypod.helpscoutdocs.com/category/134-api?sort=).

## Relationships 

#### Create a New Relationship

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
 * or relates to the created entity.
 */
$entity->id;
```

#### Retrieve a Relationship
```php 
use MonkeyPod\Api\Resources\Entity;

$idFromPreviousApiCall = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$person = new Entity($idFromPreviousApiCall);
$person->retrieve();

$person->first_name; // Jane
```

#### Update a Relationship

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

#### Delete (or Deactivate) a Relationship

```php 
use MonkeyPod\Api\Resources\Entity;

$id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$person = new Entity($id);
$person->delete();

// An entity will only be deleted in MonkeyPod if that can be done 
// without violating data integrity. If the entity has associated 
// transactions or other data, it will be deactivated instead.  
```

## Custom Attributes

#### Retrieve a Collection of Custom Attributes

COMING SOON.

## Relationship Phones

#### Retrieve a Collection of Phone Numbers for a Specific Relationship

COMING SOON.

#### Retrieve a Single Phone Number

COMING SOON.

#### Delete a Phone Number

COMING SOON.

## Relationship Interactions

#### Create a Relationship Interaction

COMING SOON.

#### Retrieve a Collection of Interactions for a Specific Relationship

COMING SOON.