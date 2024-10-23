<?php

namespace MonkeyPod\Api\Resources\Transactions;

use Carbon\Carbon;
use MonkeyPod\Api\Attributes\AccessibleProperty;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Concerns\HasMetadata;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string         $id                         The UUID of the grant transaction
 * @property string         $date                       The date of the transaction (YYYY-MM-DD)
 * @property string         $funder_id                  The UUID of the funder entity
 * @property string         $asset_account_id           The UUID of the asset account which the grant debited
 * @property ?string        $memo                       Notes about the transaction as a whole
 * @property ?array         $gift                       An array of data on the gift / deductible portion. See relevant setter methods.
 * @property ?array         $fees                       An array of data on fees or processing expenses.
 * @property ?array         $tags                       An array of tags on the grant
 * @property ?array         $class_id                   The UUID of the class applied to the grant
 * @property boolean        $restricted                 Whether or not the grant is restricted
 * @property ?string        $restricted_account_id      If the grant is restricted, the UUID of its restricted net asset account
 * @property ?array         $metadata                   Associative array of metadata associated with the grant
 * @property ?string        $created_at                 An ISO 8601 formatted timestamp when the record was created
 * @property ?string        $updated_at                 An ISO 8601 formatted timestamp when the record was last updated
 *
 * @method  Donation setMemo(string $memo)
 * @method  Donation setFunderId(string $donorId)
 * @method  Donation setAssetAccountId(string $assetAccountId)
 * @method  Donation setGiftAmount(string | float $amount)
 * @method  Donation setGiftAccountId(string $accountId)
 * @method  Donation setClassId(string $classId)
 * @method  Donation setTags(array $tagNames)
 * @method  Donation setFees(array $fees)
 *
 * @method  string      getDate()
 * @method  string      getDonorId()
 * @method  string      getAssetAccountId()
 * @method  string      getMemo()
 * @method  string      getGiftAmount()
 * @method  null|string getGiftAccountId()
 * @method  null|string getFees()
 * @method  null|string getClassId()
 * @method  null|string getTags()
 */
#[AccessibleProperty('GiftAmount', 'gift.amount')]
class Grant implements Resource
{
    use ActsAsResource;
    use HasMetadata;

    protected array $dates = [
        'date',
    ];

    public function addFee(string | float $amount, string $accountId, string $memo = ''): static
    {
        $fees = $this->get('fees') ?? [];
        $fees[] = [
            'amount' => $amount,
            'account_id' => $accountId,
            'memo' => $memo,
        ];

        return $this->set('fees', $fees);
    }

    /**
     * @deprecated
     * @see addFee()
     */
    public function setFeeAmount(string | float $amount)
    {
        return $this->set('fees.0.amount', $amount);
    }

    /**
     * @deprecated
     * @see addFee()
     */
    public function setFeeAccountId(string $accountId)
    {
        return $this->set('fees.0.account_id', $accountId);
    }

    /**
     * @deprecated
     * @see addFee()
     */
    public function setFeeMemo(string $memo)
    {
        return $this->set('fees.0.memo', $memo);
    }

    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . "grants";
    }
}
