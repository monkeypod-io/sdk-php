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
2. The subdomain where your organization accesses MonkeyPod (e.g., "goodwork" for "goodwork.monkeypod.io")

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


```

#### Retrieve a Relationship


#### Update a Relationship


#### Delete (or Deactivate) a Relationship


## Custom Attributes

#### Retrieve a Collection of Custom Attributes


## Relationship Phones

#### Retrieve a Collection of Phone Numbers for a Specific Relationship

#### Retrieve a Single Phone Number

#### Delete a Phone Number


## Relationship Interactions

#### Create a Relationship Interaction

#### Retrieve a Collection of Interactions for a Specific Relationship