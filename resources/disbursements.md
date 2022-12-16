---
layout: default
parent: API Resources
nav_order: 20
---

## Disbursements

**Note:** Disbursements are not an available transaction type for all MonkeyPod users. 
They require that your Chart of Accounts includes "virtual" asset and income accounts, 
which are created by certain apps but have no formal accounting meaning.

### Create a Disbursement

```php 
use MonkeyPod\Api\Resources\Transactions\Disbursement;

$resource = new Disbursement();
$resource->date = "2020-02-24";
$resource->amount = "100.00";
$resource->virtual_asset_account_id = "96f40557-cb14-415a-b306-bcafb8032a7c";
$resource->virtual_income_account_id = "96f4056b-1a4a-4774-8be0-f49eb1bb7a30";
$resource->asset_account_id = "96f40579-cf44-4463-81c5-742661f39a2c";
$resource->income_account_id = "96f40585-f004-45ec-b97e-dad19a68d221";
$resource->create();

// Capture the URL to view disbursement in MonkeyPod
$resource->getLink('self');

```

