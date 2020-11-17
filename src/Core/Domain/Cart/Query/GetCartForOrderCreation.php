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
    private $separateGiftProducts = false;

    /**
     * @var bool
     */
    private $separateGiftCartRules = false;

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
    public function separateGiftProducts(): bool
    {
        return $this->separateGiftProducts;
    }

    /**
     * When separateGiftProducts is set to TRUE, payed products lines are separated to gift products.
     * Otherwise, we will have one line per product and the gifted quantity will be included to the product quantity in the cart.
     *
     * @param bool $separateGiftProducts
     *
     * @return GetCartForOrderCreation
     */
    public function setSeparateGiftProducts(bool $separateGiftProducts): GetCartForOrderCreation
    {
        $this->separateGiftProducts = $separateGiftProducts;

        return $this;
    }

    /**
     * @return bool
     */
    public function separateGiftCartRules(): bool
    {
        return $this->separateGiftCartRules;
    }

    /**
     * When separateGiftCartRules is set to TRUE, we will have a specific cart rule entry with the price of the gifted products.
     * Otherwise, this price will be included to the overall discounts entry.
     *
     * @param bool $separateGiftCartRules
     *
     * @return GetCartForOrderCreation
     */
    public function setSeparateGiftCartRules(bool $separateGiftCartRules): GetCartForOrderCreation
    {
        $this->separateGiftCartRules = $separateGiftCartRules;

        return $this;
    }
}
