<?php

namespace MonkeyPod\Api\Resources\Transactions;

use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string         $id                         The UUID of the fund release transaction
 * @property string         $date                       The date of the transaction (YYYY-MM-DD)
 * @property string         $restricted_account_id      The UUID of the restricted account from which funds were released
 * @property array          $amounts                    An array of amounts
 * @property null|string    $memo                       Notes about the transaction as a whole
 * @property string         $created_at                 An ISO 8601 formatted timestamp when the record was created
 * @property string         $updated_at                 An ISO 8601 formatted timestamp when the record was last updated
 */
class FundRelease implements Resource
{
    use ActsAsResource;

    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . "fund_releases";
    }
}