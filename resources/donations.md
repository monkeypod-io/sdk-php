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

### Retrieve a Donation Collection

```php
use MonkeyPod\Api\Resources\Transactions\DonationCollection;

$donations = new DonationCollection();
$donations->withStartDate('2021-01-20');
$donations->withEndDate('2025-01-20');
$donations->retrieve();
```

##### Filtering by Fiscal Sponsee
Donation collections support a filter called ```withFiscalSponsee(string $subdomain)```. This filter
is only applicable to organizations that function as fiscal sponsors on MonkeyPod. If you are a fiscal
sponsor, you may pass the MonkeyPod subdomain for a specific fiscal sponsee to filter by donations
made in support of that project.