<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\OrderReturn\QueryResult;

/**
 * Represents a single product row of the merchandise return edit form.
 *
 * A row can either be a classic product line (customizationId === 0) or a
 * customized product line; in the latter case $customizationFields holds the
 * customer-provided data (file thumbnails, text inputs).
 */
class OrderReturnProductForEditing
{
    /**
     * @var int
     */
    private $orderDetailId;

    /**
     * @var int
     */
    private $customizationId;

    /**
     * @var string
     */
    private $reference;

    /**
     * @var string
     */
    private $productName;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var bool
     */
    private $isCustomization;

    /**
     * @var OrderReturnCustomizationFieldForEditing[]
     */
    private $customizationFields;

    /**
     * @param OrderReturnCustomizationFieldForEditing[] $customizationFields
     */
    public function __construct(
        int $orderDetailId,
        int $customizationId,
        string $reference,
        string $productName,
        int $quantity,
        bool $isCustomization,
        array $customizationFields = []
    ) {
        $this->orderDetailId = $orderDetailId;
        $this->customizationId = $customizationId;
        $this->reference = $reference;
        $this->productName = $productName;
        $this->quantity = $quantity;
        $this->isCustomization = $isCustomization;
        $this->customizationFields = $customizationFields;
    }

    public function getOrderDetailId(): int
    {
        return $this->orderDetailId;
    }

    public function getCustomizationId(): int
    {
        return $this->customizationId;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function isCustomization(): bool
    {
        return $this->isCustomization;
    }

    /**
     * @return OrderReturnCustomizationFieldForEditing[]
     */
    public function getCustomizationFields(): array
    {
        return $this->customizationFields;
    }
}
