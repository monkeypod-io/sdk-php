<?php

namespace MonkeyPod\Api\Resources\Transactions;

use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string         $type           Either "Debit" or "Credit"
 * @property string         $amount         A string representing the amount (in US dollars) of the transaction line
 * @property string         $account_id     The UUID of the account associated with the line
 * @property null|string    $class_id       The UUID of the class associated with the line
 * @property null|array     $tags           An array of tags associated with the line
 * @property null|string    $entity_id      The UUID of the entity associated with the line
 * @property null|string    $grant_id       The UUID of the grant associated with the line
 * @property null|string    $memo           A description of the line
 */
class Line implements Resource, \JsonSerializable
{
    use ActsAsResource;

    public function getBaseEndpoint(): string
    {
        throw new \InvalidArgumentException("Endpoint not supported");
    }
}