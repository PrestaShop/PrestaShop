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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartAddress;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartProduct;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartRule;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartShipping;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation\CartSummary;

/**
 * Holds cart information data
 */
class CartForOrderCreation
{
    /**
     * @var int
     */
    private $cartId;

    /**
     * @var CartProduct[]
     */
    private $products;

    /**
     * @var int
     */
    private $currencyId;

    /**
     * @var int
     */
    private $langId;

    /**
     * @var CartRule[]
     */
    private $cartRules;

    /**
     * @var CartAddress[]
     */
    private $addresses;

    /**
     * @var CartShipping|null
     */
    private $shipping;

    /**
     * @var CartSummary
     */
    private $summary;

    /**
     * @param int $cartId
     * @param array $products
     * @param int $currencyId
     * @param int $langId
     * @param CartRule[] $cartRules
     * @param CartAddress[] $addresses
     * @param CartSummary $summary
     * @param CartShipping $shipping
     */
    public function __construct(
        int $cartId,
        array $products,
        int $currencyId,
        int $langId,
        array $cartRules,
        array $addresses,
        CartSummary $summary,
        CartShipping $shipping = null
    ) {
        $this->cartId = $cartId;
        $this->products = $products;
        $this->currencyId = $currencyId;
        $this->langId = $langId;
        $this->cartRules = $cartRules;
        $this->addresses = $addresses;
        $this->shipping = $shipping;
        $this->summary = $summary;
    }

    /**
     * @return int
     */
    public function getCartId(): int
    {
        return $this->cartId;
    }

    /**
     * @return CartProduct[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    /**
     * @return int
     */
    public function getCurrencyId(): int
    {
        return $this->currencyId;
    }

    /**
     * @return int
     */
    public function getLangId(): int
    {
        return $this->langId;
    }

    /**
     * @return CartRule[]
     */
    public function getCartRules(): array
    {
        return $this->cartRules;
    }

    /**
     * @return CartAddress[]
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    /**
     * @return CartShipping|null
     */
    public function getShipping(): ?CartShipping
    {
        return $this->shipping;
    }

    /**
     * @return CartSummary
     */
    public function getSummary(): CartSummary
    {
        return $this->summary;
    }
}
