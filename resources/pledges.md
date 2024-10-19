---
layout: default
parent: API Resources
nav_order: 12
---

## Pledges

### Retrieve a Pledge Collection

```php
use MonkeyPod\Api\Resources\Transactions\PledgeCollection;

$pledges = new PledgeCollection();
$pledges->withStartDate('2021-01-20');
$pledges->withEndDate('2025-01-20');
$pledges->retrieve();
```

##### Filtering by Fiscal Sponsee
Pledge collections support a filter called ```withFiscalSponsee(string $subdomain)```. This filter
is only applicable to organizations that function as fiscal sponsors on MonkeyPod. If you are a fiscal
sponsor, you may pass the MonkeyPod subdomain for a specific fiscal sponsee to filter by pledges
made in support of that project.