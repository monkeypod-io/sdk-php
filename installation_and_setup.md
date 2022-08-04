## Installation
Use Composer to install the SDK:

```composer require monkeypod/api```

## Setup and Configuration

The two pieces of data you'll need are:
1. Your API Key
2. The subdomain where your organization accesses MonkeyPod
(e.g., "goodwork" for "goodwork.monkeypod.io")

Static configuration is the best choice for most use cases. You can run your setup and configuration 
code somewhere in your application's boot process, and then all API calls will automatically use
those settings.

```php
use MonkeyPod\Api\Client;

$apiKey = "your-api-key-from-monkeypod";
$subdomain = "my-organization-subdomain";

Client::configure($apiKey, $subdomain);
```

Alternatively, dynamic configuration scopes the configuration to a specific resource object. 
This could be useful in testing or in (rare) scenarios when different resources require different
API keys.

```php
use MonkeyPod\Api\Client;

$apiKey = "your-api-key-from-monkeypod";
$subdomain = "my-organization-subdomain";

$client = new Client();
$client->setApiKey($apiKey);
$client->setSubdomain($subdomain);

// If you use this approach, you'll need to pass the client in
// the constructor of any resources.

$entity = new \MonkeyPod\Api\Resources\Entity($client);
```
