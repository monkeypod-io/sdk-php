---
layout: default
parent: API Resources
nav_order: 11
---

## Donations

### Create a Donation

```php 
use MonkeyPod\Api\Resources\Transactions\Donation;

$donation = new Donation();
$donation->date = "2022-01-01";
$donation->donor_id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$donation->asset_account_id = "xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx";
$donation->setGiftAmount("100.00");
$donation->setGiftAccountId("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");
$donation->setNongiftAmount("50.00");
$donation->setNongiftAccountId("xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx");
$donation->setNongiftMemo("Donor received tickets to event with open bar and hors d'oeuvres");
$donation->create();
```

### Retrieve a Donation

```php 
use MonkeyPod\Api\Resources\Transactions\Donation;

$donation = new Donation($id);
$donation->retrieve();

// URL to view fund release in MonkeyPod
$donation->getLink('self');
```
