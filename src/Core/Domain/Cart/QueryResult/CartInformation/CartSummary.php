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

namespace PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartInformation;

/**
 * Holds cart summary data
 */
class CartSummary
{
    /**
     * @var string
     */
    private $totalProductsPrice;

    /**
     * @var string
     */
    private $totalDiscount;

    /**
     * @var string
     */
    private $totalShippingPrice;

    /**
     * @var string
     */
    private $totalTaxes;

    /**
     * @var string
     */
    private $totalPriceWithTaxes;

    /**
     * @var string
     */
    private $totalPriceWithoutTaxes;

    /**
     * @var string
     */
    private $orderMessage;

    /**
     * @var string
     */
    private $processOrderLink;

    /**
     * @param string $totalProductsPrice
     * @param string $totalDiscount
     * @param string $totalShippingPrice
     * @param string $totalTaxes
     * @param string $totalPriceWithTaxes
     * @param string $totalPriceWithoutTaxes
     * @param string $orderMessage
     * @param string $processOrderLink
     */
    public function __construct(
        string $totalProductsPrice,
        string $totalDiscount,
        string $totalShippingPrice,
        string $totalTaxes,
        string $totalPriceWithTaxes,
        string $totalPriceWithoutTaxes,
        string $orderMessage,
        string $processOrderLink
    ) {
        $this->totalProductsPrice = $totalProductsPrice;
        $this->totalDiscount = $totalDiscount;
        $this->totalShippingPrice = $totalShippingPrice;
        $this->totalTaxes = $totalTaxes;
        $this->totalPriceWithTaxes = $totalPriceWithTaxes;
        $this->totalPriceWithoutTaxes = $totalPriceWithoutTaxes;
        $this->processOrderLink = $processOrderLink;
        $this->orderMessage = $orderMessage;
    }

    /**
     * @return string
     */
    public function getTotalProductsPrice(): string
    {
        return $this->totalProductsPrice;
    }

    /**
     * @return string
     */
    public function getTotalDiscount(): string
    {
        return $this->totalDiscount;
    }

    /**
     * @return string
     */
    public function getTotalShippingPrice(): string
    {
        return $this->totalShippingPrice;
    }

    /**
     * @return string
     */
    public function getTotalTaxes(): string
    {
        return $this->totalTaxes;
    }

    /**
     * @return string
     */
    public function getTotalPriceWithTaxes(): string
    {
        return $this->totalPriceWithTaxes;
    }

    /**
     * @return string
     */
    public function getTotalPriceWithoutTaxes(): string
    {
        return $this->totalPriceWithoutTaxes;
    }

    /**
     * @return string
     */
    public function getProcessOrderLink(): string
    {
        return $this->processOrderLink;
    }

    /**
     * @return string
     */
    public function getOrderMessage(): string
    {
        return $this->orderMessage;
    }
}
