<?php

namespace MonkeyPod\Api\Resources\Transactions;

use MonkeyPod\Api\Attributes\AccessibleProperty;
use MonkeyPod\Api\Resources\Concerns\ActsAsResource;
use MonkeyPod\Api\Resources\Concerns\HasMetadata;
use MonkeyPod\Api\Resources\Contracts\Resource;

/**
 * @property string         $id                         The UUID of the sale transaction
 * @property string         $date                       The date of the transaction (YYYY-MM-DD)
 * @property string         $customer_id                The UUID (or numeric ID) of the customer entity
 * @property string         $deposit_to_account_id      The UUID of the asset account to which proceeds were deposited
 * @property ?string        $payment_method             How the customer paid
 * @property ?string        $ref_number                 Reference number (e.g., a check number or external order ID)
 * @property ?string        $memo                       Notes about the transaction as a whole
 * @property ?array         $items                      An array of line items from the catalog
 * @property ?array         $services                   An array of ad-hoc service lines
 * @property ?array         $fees                       An array of processing fees
 * @property ?string        $discount_id                The UUID of a discount to apply
 * @property ?string        $class_id                   The UUID of the class to apply
 * @property ?array         $tags                       An array of tags to apply to the sale
 * @property ?array         $metadata                   Associative array of metadata to associate with the sale
 * @property ?string        $created_at                 An ISO 8601 formatted timestamp when the record was created
 * @property ?string        $updated_at                 An ISO 8601 formatted timestamp when the record was last updated
 *
 * @method  Sale setMemo(string $memo)
 * @method  Sale setCustomerId(string $customerId)
 * @method  Sale setDepositToAccountId(string $accountId)
 * @method  Sale setPaymentMethod(string $paymentMethod)
 * @method  Sale setRefNumber(string $refNumber)
 * @method  Sale setItems(array $items)
 * @method  Sale setServices(array $services)
 * @method  Sale setFees(array $fees)
 * @method  Sale setDiscountId(string $discountId)
 * @method  Sale setClassId(string $classId)
 * @method  Sale setTags(array $tagNames)
 *
 * @method  string      getDate()
 * @method  string      getCustomerId()
 * @method  string      getDepositToAccountId()
 * @method  null|string getPaymentMethod()
 * @method  null|string getRefNumber()
 * @method  null|string getMemo()
 * @method  null|array  getItems()
 * @method  null|array  getServices()
 * @method  null|array  getFees()
 * @method  null|string getDiscountId()
 * @method  null|string getClassId()
 * @method  null|array  getTags()
 */
class Sale implements Resource
{
    use ActsAsResource;
    use HasMetadata;

    protected array $dates = [
        'date',
    ];

    public function addItem(string $itemId, string|float|int $quantity, string|float|null $price = null): static
    {
        $items = $this->get('items') ?? [];
        $items[] = array_filter([
            'item_id' => $itemId,
            'quantity' => $quantity,
            'price' => $price,
        ], fn ($value) => null !== $value);

        return $this->set('items', $items);
    }

    public function addService(string|float $amount, string $accountId, string $memo = ''): static
    {
        $services = $this->get('services') ?? [];
        $services[] = [
            'amount' => $amount,
            'account_id' => $accountId,
            'memo' => $memo,
        ];

        return $this->set('services', $services);
    }

    public function addFee(string|float $amount, ?string $accountId = null, string $memo = ''): static
    {
        $fees = $this->get('fees') ?? [];
        $fees[] = array_filter([
            'amount' => $amount,
            'account_id' => $accountId,
            'memo' => $memo,
        ], fn ($value) => null !== $value && '' !== $value);

        return $this->set('fees', $fees);
    }

    public function getBaseEndpoint(): string
    {
        return $this->apiClient->getBaseUri() . "sales";
    }
}
