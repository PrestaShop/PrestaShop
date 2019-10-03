<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

class CartInformation
{
    //@todo: implement DTO's instead of arrays
    /**
     * @var int
     */
    private $cartId;

    /**
     * @todo: CartProduct[]
     *
     * @var array
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
     * @var array
     */
    private $vouchers;

    /**
     * @var CartAddress[]
     */
    private $addresses;

    /**
     * @var int
     */
    private $deliveryAddressId;

    /**
     * @var int
     */
    private $invoiceAddressId;

    /**
     * @var array
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
     * @param array $vouchers
     * @param int $deliveryAddressId
     * @param int $invoiceAddressId
     * @param CartAddress[] $addresses
     * @param array $shipping
     * @param array $summary
     */
    public function __construct(
        int $cartId,
        array $products,
        int $currencyId,
        int $langId,
        array $vouchers,
        int $deliveryAddressId,
        int $invoiceAddressId,
        array $addresses,
        array $shipping,
        array $summary
    ) {
        $this->cartId = $cartId;
        $this->products = $products;
        $this->currencyId = $currencyId;
        $this->langId = $langId;
        $this->vouchers = $vouchers;
        $this->addresses = $addresses;
        $this->deliveryAddressId = $deliveryAddressId;
        $this->invoiceAddressId = $invoiceAddressId;
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
     * @return array
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
     * @return array
     */
    public function getVouchers(): array
    {
        return $this->vouchers;
    }

    /**
     * @return CartAddress[]
     */
    public function getAddresses(): array
    {
        return $this->addresses;
    }

    /**
     * @return int
     */
    public function getDeliveryAddressId(): int
    {
        return $this->deliveryAddressId;
    }

    /**
     * @return int
     */
    public function getInvoiceAddressId(): int
    {
        return $this->invoiceAddressId;
    }

    /**
     * @return array
     */
    public function getShipping(): array
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
