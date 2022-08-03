<?php

namespace MonkeyPod\Api\Resources;

use Illuminate\Support\Str;
use MonkeyPod\Api\Client;
use MonkeyPod\Api\Exception\ApiResponseError;
use MonkeyPod\Api\Exception\IncompleteConfigurationException;
use MonkeyPod\Api\Exception\InvalidResourceException;
use MonkeyPod\Api\Exception\InvalidUuidException;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string                 $id                 The entity's unique ID, a UUID
 * @property string                 $type               The type of entity, either "Individual", "Organization", "Foundation", "Government", "Corporate", or "Other"
 * @property string                 $honorific          For example, "Ms.", "Dr.", or "Rev."
 * @property string                 $first_name         The person's first name
 * @property string                 $middle_name        The person's middle name
 * @property string                 $last_name          The person's last name (required for individuals unless email is present)
 * @property string                 $pronouns           The person's preferred pronouns
 * @property array                  $aliases            An array of alternate names for the entity
 * @property string                 $title              The entity's job title
 * @property string                 $organization_name  A company or organization name for the entity (required for non-individuals)
 * @property string                 $website            The entity's website
 * @property string                 $email              The primary email address for the entity (required for individuals unless last name is present)
 * @property string                 $address            The entity's street address
 * @property string                 $city               The entity's city
 * @property string                 $state              The entity's state or similar region
 * @property string                 $postal_code        The entity's zip code or postal code
 * @property string                 $country            The entity's country
 * @property array&EntityPhone[]    $phones             An array of EntityPhone objects
 * @property array&string[]         $roles              An array of the entity's roles
 * @property array                  $extra_attributes   An array of custom attributes, keyed by the attributes' unique slugs
 * @property boolean                $active             Whether the entity is active or has been deactivated
 * @property string                 $created_at         An ISO 8601 formatted timestamp when the entity record was created
 * @property string                 $updated_at         An ISO 8601 formatted timestamp when the entity record was last updated
 */
class Entity implements Resource
{
    use ActsAsResource;

    public function interaction($interactionId = null): EntityInteraction
    {
        return EntityInteraction::forEntity($this, $interactionId);
    }

    public function interactions(): EntityInteractionCollection
    {
        return EntityInteractionCollection::forEntity($this);
    }

    public function setExtraAttribute(string $slug, mixed $value): static
    {
        $this->set("extra_attributes.$slug", $value);

        return $this;
    }

    /**
     * @throws InvalidUuidException
     * @throws IncompleteConfigurationException
     */
    public function hydrateNestedResources(): void
    {
        foreach ($this->get("phones") ?? [] as $key => $value) {
            $phone = new EntityPhone($value['id']);
            $phone->set(null, $value);

            $this->set("phones.$key", $phone);
        }
    }

    /**
     * @throws IncompleteConfigurationException
     */
    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . "entities";
    }
    
    public function getFullName(): string
    {
        if (isset($this->data['type']) && $this->data['type'] !== 'Individual') {
            return $this->data['organization_name'];
        }
        
        return str($this->data['first_name'] ?? '')
            ->append(' ')
            ->append($this->data['last_name'] ?? '')
            ->trim()
            ->toString();
    }
}