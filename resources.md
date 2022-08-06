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

### Pagination
By default, a resource collection will contain (up to) the first 15 results
from the API call. 

You may use the ```autoPagingIterator()``` method to return an iterator that 
invisibly requests additional pages of results until the last page is reached. 
