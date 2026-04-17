<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Cart\Comparator;

use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

class CartProductUpdate
{
    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var CustomizationId|null
     */
    private $customizationId;

    /**
     * @var int
     */
    private $deltaQuantity;

    /**
     * @var bool
     */
    private $created;

    /**
     * @param int $productId
     * @param int $combinationId
     * @param int $deltaQuantity
     * @param bool $created
     * @param int $customizationId
     */
    public function __construct(int $productId, int $combinationId, int $deltaQuantity, bool $created, int $customizationId = 0)
    {
        $this->productId = new ProductId($productId);
        $this->combinationId = $combinationId > 0 ? new CombinationId($combinationId) : null;
        $this->customizationId = $customizationId > 0 ? new CustomizationId($customizationId) : null;
        $this->deltaQuantity = $deltaQuantity;
        $this->created = $created;
    }

    /**
     * @param CartProductUpdate $cartProductUpdate
     *
     * @return bool
     */
    public function productMatches(CartProductUpdate $cartProductUpdate): bool
    {
        if ($this->getProductId()->getValue() !== $cartProductUpdate->getProductId()->getValue()) {
            return false;
        }
        $combinationIdValue = null !== $this->getCombinationId() ? $this->getCombinationId()->getValue() : 0;
        $checkedCombinationIdValue = null !== $cartProductUpdate->getCombinationId() ? $cartProductUpdate->getCombinationId()->getValue() : 0;

        $customizationIdValue = null !== $this->getCustomizationId() ? $this->getCustomizationId()->getValue() : 0;
        $checkedCustomizationIdValue = null !== $cartProductUpdate->getCustomizationId() ? $cartProductUpdate->getCustomizationId()->getValue() : 0;

        return $combinationIdValue === $checkedCombinationIdValue && $customizationIdValue === $checkedCustomizationIdValue;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return CustomizationId|null
     */
    public function getCustomizationId(): ?CustomizationId
    {
        return $this->customizationId;
    }

    /**
     * @return int
     */
    public function getDeltaQuantity(): int
    {
        return $this->deltaQuantity;
    }

    /**
     * @param int $deltaQuantity
     *
     * @return $this
     */
    public function setDeltaQuantity(int $deltaQuantity): self
    {
        $this->deltaQuantity = $deltaQuantity;

        return $this;
    }

    /**
     * @return bool
     */
    public function isCreated(): bool
    {
        return $this->created;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id_product' => $this->productId->getValue(),
            'id_product_attribute' => null !== $this->combinationId ? $this->combinationId->getValue() : 0,
            'id_customization' => null !== $this->customizationId ? $this->customizationId->getValue() : 0,
            'delta_quantity' => $this->deltaQuantity,
            'created' => $this->created,
        ];
    }
}
