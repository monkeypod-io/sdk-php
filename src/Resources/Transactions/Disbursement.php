<?php

namespace MonkeyPod\Api\Resources\Transactions;

use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property        string          $id                         The UUID of the disbursement transaction
 * @property        string          $date                       The date of the transaction (YYYY-mm-dd)
 * @property        string          $amount                     The amount (in US dollars) of the disbursement
 * @property        string          $asset_account_id           The UUID of the asset account to debit (increase)
 * @property        string          $income_account_id          The UUID of the income account to credit (increase)
 * @property        string          $virtual_asset_account_id   The UUID of the virtual asset account to credit (decrease/offset)
 * @property        string          $virtual_income_account_id  The UUID of the virtual income account to debit (decrease/offset)
 * @property        null|string     $memo                       An optional memo on the transaction
 * @property-read   string          $created_at                 A timestamp when the record was created
 * @property-read   string          $updated_at                 A timestamp when the record was last updated
 */
class Disbursement implements Resource
{
    use ActsAsResource;

    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . 'disbursements';
    }
}