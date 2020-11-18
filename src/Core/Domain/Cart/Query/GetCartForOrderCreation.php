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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\Query;

use PrestaShop\PrestaShop\Core\Domain\Cart\Exception\CartConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Cart\ValueObject\CartId;

/**
 * Query for getting cart information
 */
class GetCartForOrderCreation
{
    /**
     * @var CartId
     */
    private $cartId;

    /**
     * @var bool
     */
    private $hideDiscounts = false;

    /**
     * @param int $cartId
     *
     * @throws CartConstraintException
     */
    public function __construct(int $cartId)
    {
        $this->cartId = new CartId($cartId);
    }

    /**
     * @return CartId
     */
    public function getCartId(): CartId
    {
        return $this->cartId;
    }

    /**
     * @return bool
     */
    public function hideDiscounts(): bool
    {
        return $this->hideDiscounts;
    }

    /**
     * When hideDiscounts is set to TRUE,
     * Payed products lines are separated to gift products
     * Price of the gifted products are removed from the overall discounts
     * Shipping is set to 0 if there is a free_shipping cart rule
     *
     * Otherwise,
     * We will have one line per product and the gifted quantity will be included to the product quantity in the cart.
     * The shipping will have his original value and a cart_rule with the shipping value will be added
     * and the gift products price will be included to the discount
     *
     * @param bool $hideDiscounts
     *
     * @return GetCartForOrderCreation
     */
    public function setHideDiscounts(bool $hideDiscounts): GetCartForOrderCreation
    {
        $this->hideDiscounts = $hideDiscounts;

        return $this;
    }
}
