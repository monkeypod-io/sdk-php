---
layout: default
nav_order: 3
has_children: true
---

# API Resources
Not all MonkeyPod data is available in the API, and not all actions are 
available for each resource. As new resources are surfaced in the API
we will do our best to keep the SDK current.

### Constructors
Most resource constructors can accept the following parameters, in any order:
* a Client object to use for API calls
* a resource ID (must be a properly-formatted UUID)

### Required, optional, and additional/unlisted fields
Resource properties are documented in the phpdoc comments on the resource 
class. If a property is designated as nullable, then it may be considered 
optional. The one exception is resource IDs, which are required but will 
be auto-assigned by MonkeyPod when creating a new resource unless you provide
your own.

There are a few cases when all fields are not documented on the resource
class. For example, ```Entity``` resources have an ```extra_attributes``` property
that includes all relevant custom attributes assigned to that entity. Because
each MonkeyPod organization has its own custom attributes, these vary from
organization to organization and cannot be centrally documented.

### Getters and Setters
Resource properties can be set or retrieved directly or through the use
of getter and methods (either magic or explicitly defined). These are 
sometimes documented in a resource class's phpdoc comments, which may 
be helpful when a resource relies on complex, nested data structures.

Setters support fluent method-chaining, such as:

```php 
$donation
    ->setGiftAmount("100.00")
    ->setGiftMemo("a crisp new Ben Franklin")
    ->setTags(["Cash Gift", "Annual Campaign"])
```

### Resource links
MonkeyPod's API often includes metadata about URLs relevant to a particular
resource. These links can be accessed by key, as in:

```php 
// The 'self' link, when present, is a URL in MonkeyPod where the record can be viewed
$resource->getLink('self');
```

## Resource Collections
Resource collection classes implement the ```ResourceCollection``` interface and
are created when you request a group of records from the API.

### Filters
When retrieving a resource collection, you will often have the option of
filtering results based on one or more search criteria. Available filters
are documented as methods on resource collection classes, and follow the naming
convention ```withXyz()```.

For example, when retrieving a collection of Accounts, you can filter results
by the type and/or subtype of Account:

```php 
$accounts = new AccountCollection();
$accounts->withType("Expense");
$accounts->withSubtype("Operating");
$accounts->retrieve();
```

Filters may also be chained:

```php 
$accounts = (new AccountCollection())
    ->withType("Expense")
    ->withSubtype("Operating")
    ->retrieve();
```

### Pagination
By default, a resource collection will contain (up to) the first 15 results
from the API call. 

You may use the ```autoPagingIterator()``` method to return an iterator that 
invisibly requests additional pages of results until the last page is reached. 
