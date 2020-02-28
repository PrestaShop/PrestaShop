<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
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
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Order\DTO;

use Order;
use PrestaShop\Decimal\Number;

/**
 * Data transfer object helping to use Number for order total prices calculation
 */
final class OrderTotalNumbers
{
    /**
     * @var Number
     */
    private $totalPaid;

    /**
     * @var Number
     */
    private $totalPaidTaxExcl;

    /**
     * @var Number
     */
    private $totalPaidTaxIncl;

    /**
     * @var Number
     */
    private $totalProducts;

    /**
     * @var Number
     */
    private $totalProductsWt;

    /**
     * @var Number
     */
    private $totalWrapping;

    /**
     * @var Number
     */
    private $totalWrappingTaxExcl;

    /**
     * @var Number
     */
    private $totalWrappingTaxIncl;

    /**
     * @var Number
     */
    private $totalDiscounts;

    /**
     * @var Number
     */
    private $totalDiscountTaxExcl;

    /**
     * @var Number
     */
    private $totalDiscountTaxIncl;

    /**
     * @var Number
     */
    private $totalShipping;

    /**
     * @var Number
     */
    private $totalShippingTaxExcl;

    /**
     * @var Number
     */
    private $totalShippingTaxIncl;

    /**
     * Static factory should be used to create the class
     *
     * @param Number $totalPaid
     * @param Number $totalPaidTaxExcl
     * @param Number $totalPaidTaxIncl
     * @param Number $totalProducts
     * @param Number $totalProductsWt
     * @param Number $totalWrapping
     * @param Number $totalWrappingTaxExcl
     * @param Number $totalWrappingTaxIncl
     * @param Number $totalDiscounts
     * @param Number $totalDiscountTaxExcl
     * @param Number $totalDiscountTaxIncl
     * @param Number $totalShipping
     * @param Number $totalShippingTaxExcl
     * @param Number $totalShippingTaxIncl
     */
    private function __construct(
        Number $totalPaid,
        Number $totalPaidTaxExcl,
        Number $totalPaidTaxIncl,
        Number $totalProducts,
        Number $totalProductsWt,
        Number $totalWrapping,
        Number $totalWrappingTaxExcl,
        Number $totalWrappingTaxIncl,
        Number $totalDiscounts,
        Number $totalDiscountTaxExcl,
        Number $totalDiscountTaxIncl,
        Number $totalShipping,
        Number $totalShippingTaxExcl,
        Number $totalShippingTaxIncl
    ) {
        $this->totalPaid = $totalPaid;
        $this->totalPaidTaxExcl = $totalPaidTaxExcl;
        $this->totalPaidTaxIncl = $totalPaidTaxIncl;
        $this->totalProducts = $totalProducts;
        $this->totalProductsWt = $totalProductsWt;
        $this->totalWrapping = $totalWrapping;
        $this->totalWrappingTaxExcl = $totalWrappingTaxExcl;
        $this->totalWrappingTaxIncl = $totalWrappingTaxIncl;
        $this->totalDiscounts = $totalDiscounts;
        $this->totalDiscountTaxExcl = $totalDiscountTaxExcl;
        $this->totalDiscountTaxIncl = $totalDiscountTaxIncl;
        $this->totalShipping = $totalShipping;
        $this->totalShippingTaxExcl = $totalShippingTaxExcl;
        $this->totalShippingTaxIncl = $totalShippingTaxIncl;
    }

    /**
     * @param Order $order
     *
     * @return OrderTotalNumbers
     */
    public static function buildFromOrder(Order $order): OrderTotalNumbers
    {
        return new self(
            new Number((string) $order->total_paid),
            new Number((string) $order->total_paid_tax_excl),
            new Number((string) $order->total_paid_tax_incl),
            new Number((string) $order->total_products),
            new Number((string) $order->total_products_wt),
            new Number((string) $order->total_wrapping),
            new Number((string) $order->total_wrapping_tax_excl),
            new Number((string) $order->total_wrapping_tax_incl),
            new Number((string) $order->total_discounts),
            new Number((string) $order->total_discounts_tax_excl),
            new Number((string) $order->total_discounts_tax_incl),
            new Number((string) $order->total_shipping),
            new Number((string) $order->total_shipping_tax_excl),
            new Number((string) $order->total_shipping_tax_incl)
        );
    }

    /**
     * @return Number
     */
    public function getTotalPaid(): Number
    {
        return $this->totalPaid;
    }

    /**
     * @return Number
     */
    public function getTotalPaidTaxExcl(): Number
    {
        return $this->totalPaidTaxExcl;
    }

    /**
     * @return Number
     */
    public function getTotalPaidTaxIncl(): Number
    {
        return $this->totalPaidTaxIncl;
    }

    /**
     * @return Number
     */
    public function getTotalProducts(): Number
    {
        return $this->totalProducts;
    }

    /**
     * @return Number
     */
    public function getTotalProductsWt(): Number
    {
        return $this->totalProductsWt;
    }

    /**
     * @return Number
     */
    public function getTotalWrapping(): Number
    {
        return $this->totalWrapping;
    }

    /**
     * @return Number
     */
    public function getTotalWrappingTaxExcl(): Number
    {
        return $this->totalWrappingTaxExcl;
    }

    /**
     * @return Number
     */
    public function getTotalWrappingTaxIncl(): Number
    {
        return $this->totalWrappingTaxIncl;
    }

    /**
     * @return Number
     */
    public function getTotalDiscounts(): Number
    {
        return $this->totalDiscounts;
    }

    /**
     * @return Number
     */
    public function getTotalDiscountTaxExcl(): Number
    {
        return $this->totalDiscountTaxExcl;
    }

    /**
     * @return Number
     */
    public function getTotalDiscountTaxIncl(): Number
    {
        return $this->totalDiscountTaxIncl;
    }

    /**
     * @return Number
     */
    public function getTotalShipping(): Number
    {
        return $this->totalShipping;
    }

    /**
     * @return Number
     */
    public function getTotalShippingTaxExcl(): Number
    {
        return $this->totalShippingTaxExcl;
    }

    /**
     * @return Number
     */
    public function getTotalShippingTaxIncl(): Number
    {
        return $this->totalShippingTaxIncl;
    }
}
