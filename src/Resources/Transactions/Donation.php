<?php

namespace MonkeyPod\Api\Resources\Transactions;

use Carbon\Carbon;
use MonkeyPod\Api\Attributes\AccessibleProperty;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Concerns\HasMetadata;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string         $id                         The UUID of the fund donation transaction
 * @property string         $date                       The date of the transaction (YYYY-MM-DD)
 * @property string         $donor_id                   The UUID of the donor entity
 * @property string         $asset_account_id           The UUID of the asset account to which the donation was deposited
 * @property ?string        $memo                       Notes about the transaction as a whole
 * @property ?array         $gift                       An array of data on the gift / deductible portion. See relevant setter methods.
 * @property ?array         $nongift                    An array of data on a nongift / nondeductible portion. See relevant setter methods.
 * @property ?array         $fees                       An array of data on fees or processing expenses.
 * @property ?array         $tags                       An array of tags to apply to the donation
 * @property ?array         $class_id                   The UUID of the class to apply to the donation
 * @property ?array         $metadata                   Associative array of metadata to associate with the donation
 * @property ?string        $created_at                 An ISO 8601 formatted timestamp when the record was created
 * @property ?string        $updated_at                 An ISO 8601 formatted timestamp when the record was last updated
 *
 * @method  Donation setMemo(string $memo)
 * @method  Donation setDonorId(string $donorId)
 * @method  Donation setAssetAccountId(string $assetAccountId)
 * @method  Donation setGiftAmount(string | float $amount)
 * @method  Donation setGiftAccountId(string $accountId)
 * @method  Donation setGiftMemo(string $memo)
 * @method  Donation setNongiftAmount(string | float $amount)
 * @method  Donation setNongiftAccountId(string $accountId)
 * @method  Donation setNongiftMemo(string $memo)
 * @method  Donation setClassId(string $classId)
 * @method  Donation setTags(array $tagNames)
 * @method  Donation setFees(array $fees)
 * @method  Donation setSendReceipt(bool $shouldSend = true)        Set to false if normal receipt sending should be omitted
 *
 * @method  string      getDate()
 * @method  string      getDonorId()
 * @method  string      getAssetAccountId()
 * @method  string      getMemo()
 * @method  string      getGiftAmount()
 * @method  null|string getGiftAccountId()
 * @method  null|string getGiftMemo()
 * @method  null|string getNongiftAmount()
 * @method  null|string getNongiftAccountId()
 * @method  null|string getNongiftMemo()
 * @method  null|string getFees()
 * @method  null|string getClassId()
 * @method  null|string getTags()
 */
#[AccessibleProperty('GiftAmount', 'gift.amount')]
#[AccessibleProperty('GiftAccountId', 'gift.account_id')]
#[AccessibleProperty('GiftMemo', 'gift.memo')]
#[AccessibleProperty('NongiftAmount', 'nongift.amount')]
#[AccessibleProperty('NongiftAccountId', 'nongift.account_id')]
#[AccessibleProperty('NongiftMemo', 'nongift.memo')]
#[AccessibleProperty('Fees', 'fees')]
#[AccessibleProperty('SendReceipt', 'send_receipt')]
class Donation implements Resource
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
        return $this->apiClient->getBaseUri() . "donations";
    }
}
