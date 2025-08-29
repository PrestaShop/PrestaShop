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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;

/**
 * Holds cart summary data
 */
class CartSummary
{
    /**
     * @param string $totalProductsPrice
     * @param string $totalDiscount
     * @param string $totalShippingPrice
     * @param string $totalShippingWithoutTaxes
     * @param string $totalTaxes
     * @param string $totalPriceWithTaxes
     * @param string $totalPriceWithoutTaxes
     * @param string $orderMessage
     * @param string $processOrderLink
     */
    public function __construct(
        private string $totalProductsPrice,
        private string $totalDiscount,
        private string $totalShippingPrice,
        private string $totalShippingWithoutTaxes,
        private string $totalTaxes,
        private string $totalPriceWithTaxes,
        private string $totalPriceWithoutTaxes,
        private string $orderMessage,
        private string $processOrderLink,
    ) {
    }

    /**
     * @return string
     */
    public function getTotalProductsPrice(): string
    {
        return $this->totalProductsPrice;
    }

    public function getTotalDiscount(): string
    {
        return $this->totalDiscount;
    }

    public function getTotalShippingPrice(): string
    {
        return $this->totalShippingPrice;
    }

    public function getTotalShippingWithoutTaxes(): string
    {
        return $this->totalShippingWithoutTaxes;
    }

    public function getTotalTaxes(): string
    {
        return $this->totalTaxes;
    }

    public function getTotalPriceWithTaxes(): string
    {
        return $this->totalPriceWithTaxes;
    }

    public function getTotalPriceWithoutTaxes(): string
    {
        return $this->totalPriceWithoutTaxes;
    }

    public function getProcessOrderLink(): string
    {
        return $this->processOrderLink;
    }

    public function getOrderMessage(): string
    {
        return $this->orderMessage;
    }
}
