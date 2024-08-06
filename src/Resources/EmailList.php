<?php

namespace MonkeyPod\Api\Resources;

use MonkeyPod\Api\Exception\ResourceNotFoundException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property    string      $id                     The email list's UUID
 * @property    string      $name                   The list name
 * @property    string      $default_from_email     The default email address from which messages are sent
 * @property    string      $default_from_name      The default name from which messages are sent
 * @property    int         $subscriber_count       The number of active subscribers
 * @property    string      $created_at             An ISO 8601 formatted timestamp when the email list was created
 * @property    string      $updated_at             An ISO 8601 formatted timestamp when the email list was last updated
 */
class EmailList implements Resource
{
    use ActsAsResource;

    public function retrieveByName(string $name): static
    {
        $collection = new EmailListCollection();
        $collection->withName($name);
        $collection->retrieve();

        if (1 !== $collection->count()) {
            throw new ResourceNotFoundException();
        }

        $this->data = $collection->first()->get();

        return $this;
    }

    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . 'email_lists';
    }
}
