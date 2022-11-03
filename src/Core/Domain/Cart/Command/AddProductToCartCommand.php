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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Responsible for adding product to cart
 */
class AddProductToCartCommand
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var ProductId
     */
    private $productId;

    /**
     * @var int
     */
    private $quantity;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var array key-value pairs where key is customizationFieldId and value is customization field value
     */
    private $customizationsByFieldIds;

    /**
     * @param int $cartId
     * @param int $productId
     * @param int $quantity
     * @param int|null $combinationId
     * @param array $customizationsByFieldIds
     *
     * @throws CartConstraintException
     */
    public function __construct(
        int $cartId,
        int $productId,
        int $quantity,
        ?int $combinationId = null,
        array $customizationsByFieldIds = []
    ) {
        $this->assertQtyIsPositive($quantity);
        $this->setCombinationId($combinationId);
        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->quantity = $quantity;
        $this->customizationsByFieldIds = $customizationsByFieldIds;
    }

    /**
     * @return CartId
     */
    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    /**
     * @return ProductId
     */
    public function getProductId(): ProductId
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return CombinationId|null
     */
    public function getCombinationId(): ?CombinationId
    {
        return $this->combinationId;
    }

    /**
     * @return array
     */
    public function getCustomizationsByFieldIds(): array
    {
        return $this->customizationsByFieldIds;
    }

    /**
     * @param int $qty
     *
     * @throws CartConstraintException
     */
    private function assertQtyIsPositive(int $qty)
    {
        if (0 >= $qty) {
            throw new CartConstraintException(sprintf('Quantity must be positive integer. "%s" given.', $qty), CartConstraintException::INVALID_QUANTITY);
        }
    }

    /**
     * @param int|null $combinationId
     */
    private function setCombinationId(?int $combinationId)
    {
        if (null !== $combinationId) {
            $combinationId = new CombinationId($combinationId);
        }

        $this->combinationId = $combinationId;
    }
}
