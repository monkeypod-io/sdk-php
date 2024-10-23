---
layout: default
parent: API Resources
nav_order: 13
---

## Grants

### Retrieve a Grant Collection

```php
use MonkeyPod\Api\Resources\Transactions\GrantCollection;

$grants = new GrantCollection();
$grants->withStartDate('2021-01-20');
$grants->withEndDate('2025-01-20');
$grants->withRestriction(true);
$grants->retrieve();
```

##### Filtering by Fiscal Sponsee
Grant collections support a filter called ```withFiscalSponsee(string $subdomain)```. This filter
is only applicable to organizations that function as fiscal sponsors on MonkeyPod. If you are a fiscal
sponsor, you may pass the MonkeyPod subdomain for a specific fiscal sponsee to filter by pledges
made in support of that project.