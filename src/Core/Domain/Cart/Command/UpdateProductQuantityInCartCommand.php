<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Command;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\QuantityAction;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;

/**
 * Updates product quantity in cart
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
    private $quantity;

    /**
     * @var QuantityAction
     */
    private $action;

    /**
     * @var int|null
     */
    private $combinationId;

    /**
     * @var int|null
     */
    private $customizationId;

    /**
     * @param int $cartId
     * @param int $productId
     * @param int $quantity
     * @param string $action
     * @param int|null $combinationId
     * @param int|null $customizationId
     */
    public function __construct(
        $cartId,
        $productId,
        $quantity,
        $action,
        $combinationId = null,
        $customizationId = null
    ) {
        $this->assertCombinationIdIsPositiveIntOrNull($combinationId);
        $this->assertCustomizationIdIsPositiveIntOrNull($customizationId);
        $this->assertQuantityIsPositiveInt($quantity);

        $this->cartId = new CartId($cartId);
        $this->productId = new ProductId($productId);
        $this->quantity = $quantity;
        $this->action = new QuantityAction($action);
        $this->combinationId = $combinationId;
        $this->customizationId = $customizationId;
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
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @return QuantityAction
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @return int|null
     */
    public function getCombinationId()
    {
        return $this->combinationId;
    }

    /**
     * @return int|null
     */
    public function getCustomizationId()
    {
        return $this->customizationId;
    }

    /**
     * @param int|null $combinationId
     *
     * @throws CartConstraintException
     */
    private function assertCombinationIdIsPositiveIntOrNull($combinationId)
    {
        if (null !== $combinationId && (!is_int($combinationId) || 0 >= $combinationId)) {
            throw new CartConstraintException(sprintf(
                'Combination id must be of type "int" and positive number, but %s given.',
                var_export($combinationId, true)
            ));
        }
    }

    /**
     * @param int|null $customizationId
     *
     * @throws CartConstraintException
     */
    private function assertCustomizationIdIsPositiveIntOrNull($customizationId)
    {
        if (null !== $customizationId && (!is_int($customizationId) || 0 >= $customizationId)) {
            throw new CartConstraintException(sprintf(
                'Customization id must be of type "int" and positive number, but %s given.',
                var_export($customizationId, true)
            ));
        }
    }

    /**
     * @param int $quantity
     */
    private function assertQuantityIsPositiveInt($quantity)
    {
        if (!is_int($quantity) || 0 > $quantity) {
            throw new CartConstraintException(sprintf(
                'Quantity must be of type "int" and positive number, but %s given.',
                var_export($quantity, true)
            ));
        }
    }
}
