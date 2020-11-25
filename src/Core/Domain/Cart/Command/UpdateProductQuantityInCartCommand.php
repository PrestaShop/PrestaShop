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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Product\Combination\ValueObject\CombinationId;
use PrestaShop\PrestaShop\Core\Domain\Product\Customization\ValueObject\CustomizationId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates product quantity in cart
 * Quantity given must include gift product
 */
class UpdateProductQuantityInCartCommand
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
    private $newQuantity;

    /**
     * @var CombinationId|null
     */
    private $combinationId;

    /**
     * @var CustomizationId|null
     */
    private $customizationId;

    /**
     * @param int $cartId
     * @param int $productId
     * @param int $quantity
     * @param int|null $combinationId
     * @param int|null $customizationId
     *
     * @throws CartConstraintException
     * @throws CartException
     */
    public function __construct(
        $cartId,
        $productId,
        $quantity,
        $combinationId = null,
        $customizationId = null
    ) {
        $this->setCombinationId($combinationId);
        $this->setCustomizationId($customizationId);
        $this->assertQuantityIsPositive($quantity);

        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->newQuantity = $quantity;
    }

    /**
     * @return CartId
     */
    public function getCartId()
    {
        return $this->cartId;
    }

    /**
     * @return ProductId
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getNewQuantity()
    {
        return $this->newQuantity;
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
     * @param int|null $combinationId
     */
    private function setCombinationId(?int $combinationId)
    {
        if (null !== $combinationId) {
            $combinationId = new CombinationId($combinationId);
        }

        $this->combinationId = $combinationId;
    }

    /**
     * @param int|null $customizationId
     */
    private function setCustomizationId(?int $customizationId)
    {
        if (null !== $customizationId) {
            $customizationId = new CustomizationId($customizationId);
        }

        $this->customizationId = $customizationId;
    }

    /**
     * @param int $qty
     *
     * @throws CartConstraintException
     */
    private function assertQuantityIsPositive(int $qty)
    {
        if (0 >= $qty) {
            throw new CartConstraintException(sprintf('Quantity must be positive integer. "%s" given.', $qty), CartConstraintException::INVALID_QUANTITY);
        }
    }
}
