# MonkeyPod API - PHP SDK
Official PHP libraries for communicating with the MonkeyPod API.

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

Client::configure($apiKey, $subdomain);

```

# API Resources
Not all MonkeyPod data is available in the API, and not all actions are 
available for each resource. As new resources are surfaced in the API
we will do our best to keep the SDK current.

Required fields will be labeled. Other fields can be presumed to be optional.

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
$id = $entity->id;
```

**NOTE:** The above is a sample of available fields, including all required fields. Other 
optional fields are available and correspond to the 
[documentation](https://monkeypod.helpscoutdocs.com/article/135-api-resource-entities?preview=6281b42168d51e7794440884).

#### Retrieve a Relationship
```php 
use MonkeyPod\Api\Resources\Entity;

$idFromPreviousApiCall = "960a735b-3ee9-4440-9c6d-25cbf27c77fe";
$person = new Entity($idFromPreviousApiCall);
$person->retrieve();

$person->first_name; // Jane
```

#### Update a Relationship

IN DEVELOPMENT.

#### Delete (or Deactivate) a Relationship

IN DEVELOPMENT.

## Custom Attributes

#### Retrieve a Collection of Custom Attributes

IN DEVELOPMENT.

## Relationship Phones

#### Retrieve a Collection of Phone Numbers for a Specific Relationship

IN DEVELOPMENT.

#### Retrieve a Single Phone Number

IN DEVELOPMENT.

#### Delete a Phone Number

IN DEVELOPMENT.

## Relationship Interactions

#### Create a Relationship Interaction

IN DEVELOPMENT.

#### Retrieve a Collection of Interactions for a Specific Relationship

IN DEVELOPMENT.