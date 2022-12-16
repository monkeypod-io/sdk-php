---
layout: default
parent: API Resources
nav_order: 19
---

## Fund Releases

### Create a Fund Release

```php 
use MonkeyPod\Api\Resources\Transactions\FundRelease;

$fundRelease = new FundRelease();
$fundRelease->restricted_account_id = "96f1b18b-a15f-454a-b33a-ad5b8f2897bc";
$fundRelease->amounts = [
    "total" => "2500.00",
];
$fundRelease->date = "2022-01-15";
$fundRelease->memo = "To cover overhead expenses permitted by funder";
$fundRelease->create();
```

### Retrieve a Fund Release

```php 
use MonkeyPod\Api\Resources\Transactions\FundRelease;

$fundRelease = new FundRelease($id);
$fundRelease->retrieve();

// URL to view fund release in MonkeyPod
$fundRelease->getLink('self');
```
