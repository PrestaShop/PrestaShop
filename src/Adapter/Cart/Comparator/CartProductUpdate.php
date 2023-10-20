<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
