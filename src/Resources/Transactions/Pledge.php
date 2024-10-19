<?php

namespace MonkeyPod\Api\Resources\Transactions;

use MonkeyPod\Api\Attributes\AccessibleProperty;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Concerns\HasMetadata;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string         $id                         The UUID of the fund donation transaction
 * @property string         $date                       The date of the transaction (YYYY-MM-DD)
 * @property string         $due_on                     The date when the pledge is due to be fully paid (YYYY-MM-DD)
 * @property string         $donor_id                   The UUID of the donor entity
 * @property ?string        $memo                       Notes about the transaction as a whole
 * @property ?array         $gift                       An array of data on the gift. See relevant setter methods.
 * @property ?array         $receivable                 An array of data on the receivable. See relevant setter methods.
 * @property ?array         $metadata                   Associative array of metadata to associate with the donation
 * @property ?string        $created_at                 An ISO 8601 formatted timestamp when the record was created
 * @property ?string        $updated_at                 An ISO 8601 formatted timestamp when the record was last updated
 *
 * @method  Pledge      setMemo(string $memo)
 * @method  Pledge      setDonorId(string $donorId)
 * @method  Pledge      setGiftAmount(string | float $amount)
 * @method  Pledge      setGiftAccountId(string $accountId)
 * @method  Pledge      setReceivableAccountId()
 *
 * @method  string      getDate()
 * @method  string      getDonorId()
 * @method  string      getAssetAccountId()
 * @method  string      getMemo()
 * @method  string      getGiftAmount()
 * @method  null|string getGiftAccountId()
 * @method  null|string getReceivableAccountId()
 * @method  null|string getReceivableOutstandingDue()
 */
#[AccessibleProperty('GiftAmount', 'gift.amount')]
#[AccessibleProperty('GiftAccountId', 'gift.account_id')]
#[AccessibleProperty('ReceivableAccountId', 'receivable.account_id')]
#[AccessibleProperty('ReceivableOutstandingDue', 'receivable.outstanding_due')]
class Pledge implements Resource
{
    use ActsAsResource;
    use HasMetadata;

    protected array $dates = [
        'date',
        'due_on'
    ];

    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . 'pledges';
    }
}
