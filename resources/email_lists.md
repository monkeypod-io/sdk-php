---
layout: default
parent: API Resources
title: Email Lists
nav_order: 5
---

## Email Lists

### Retrieve an Email List

```php 
use MonkeyPod\Api\Resources\EmailList;

$list = new EmailList($id);
$list->retrieve();
```

### Retrieve an Email List by Name

```php 
use MonkeyPod\Api\Resources\EmailList;

$list = new EmailList();
$list->retrieveByName("Newsletter");
```

### Retrieve a Collection of All Your Email Lists
```php 
use MonkeyPod\Api\Resources\EmailListCollection;

$collection = new EmailListCollection();
$collection->retrieve();

foreach ($collection as $emailList) {
    assert($emailList instanceof MonkeyPod\Api\Resources\EmailList::class);
}

```
