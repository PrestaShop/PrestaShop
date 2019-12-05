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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult;

use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartAddress;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartProduct;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartRule;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation\CartShipping;

class CartInformation
{
    //@todo: implement DTO's instead of arrays
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
     * @var array
     */
    private $summary;

    /**
     * @param int $cartId
     * @param array $products
     * @param int $currencyId
     * @param int $langId
     * @param CartRule[] $cartRules
     * @param CartAddress[] $addresses
     * @param CartShipping $shipping
     * @param array $summary
     */
    public function __construct(
        int $cartId,
        array $products,
        int $currencyId,
        int $langId,
        array $cartRules,
        array $addresses,
        CartShipping $shipping = null,
        array $summary = null
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
     * @return array
     */
    public function getSummary(): array
    {
        return $this->summary;
    }
}
