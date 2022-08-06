---
layout: default
parent: API Resources
title: Entities
nav_order: 5
---

## Accounts

### Retrieve an Account

```php 
use MonkeyPod\Api\Resources\Account;

$account = new Account($id);
$account->retrieve();
```

### Retrieve an Account by Name

```php 
use MonkeyPod\Api\Resources\Account;

$account = new Account();
$account->retrieveByName("Checking");
```

### Retrieve an Account by Number

```php 
use MonkeyPod\Api\Resources\Account;

$account = new Account();
$account->retrieveByNumber(8001);
```

### Retrieve a Collection of All Your Accounts
```php 
use MonkeyPod\Api\Resources\AccountCollection;

$collection = new AccountCollection();
$collection->retrieve();

foreach ($collection as $account) {
    $account instanceof MonkeyPod\Api\Resources\Account::class; // true
}

```

### Retrieve a Collection of Accounts Filtered by Type
```php 
use MonkeyPod\Api\Resources\AccountCollection;

$collection = new AccountCollection();
$collection->withType("Expense");
$collection->retrieve();

foreach ($collection as $account) {
    "Expense" === $account->type; // true
}

```

### Retrieve a Collection of Accounts Filtered by Subtype
```php 
use MonkeyPod\Api\Resources\AccountCollection;

$collection = new AccountCollection();
$collection->withSubtype("Contributed");
$collection->retrieve();

foreach ($collection as $account) {
    "Contributed" === $account->subtype; // true
}

```