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

namespace PrestaShop\PrestaShop\Adapter\Invoice\DTO;

use OrderInvoice;
use PrestaShop\Decimal\Number;

/**
 * Data transfer object helping to use Number for invoice total prices calculation
 */
final class InvoiceTotalNumbers
{
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
    private $totalWrappingTaxExcl;

    /**
     * @var Number
     */
    private $totalWrappingTaxIncl;

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
    private $totalShippingTaxExcl;

    /**
     * @var Number
     */
    private $totalShippingTaxIncl;

    /**
     * Static factory should be used to create the class
     *
     * @param Number $totalPaidTaxExcl
     * @param Number $totalPaidTaxIncl
     * @param Number $totalProducts
     * @param Number $totalProductsWt
     * @param Number $totalWrappingTaxExcl
     * @param Number $totalWrappingTaxIncl
     * @param Number $totalDiscountTaxExcl
     * @param Number $totalDiscountTaxIncl
     * @param Number $totalShippingTaxExcl
     * @param Number $totalShippingTaxIncl
     */
    private function __construct(
        Number $totalPaidTaxExcl,
        Number $totalPaidTaxIncl,
        Number $totalProducts,
        Number $totalProductsWt,
        Number $totalWrappingTaxExcl,
        Number $totalWrappingTaxIncl,
        Number $totalDiscountTaxExcl,
        Number $totalDiscountTaxIncl,
        Number $totalShippingTaxExcl,
        Number $totalShippingTaxIncl
    ) {
        $this->totalPaidTaxExcl = $totalPaidTaxExcl;
        $this->totalPaidTaxIncl = $totalPaidTaxIncl;
        $this->totalProducts = $totalProducts;
        $this->totalProductsWt = $totalProductsWt;
        $this->totalWrappingTaxExcl = $totalWrappingTaxExcl;
        $this->totalWrappingTaxIncl = $totalWrappingTaxIncl;
        $this->totalDiscountTaxExcl = $totalDiscountTaxExcl;
        $this->totalDiscountTaxIncl = $totalDiscountTaxIncl;
        $this->totalShippingTaxExcl = $totalShippingTaxExcl;
        $this->totalShippingTaxIncl = $totalShippingTaxIncl;
    }

    /**
     * @param OrderInvoice $invoice
     *
     * @return InvoiceTotalNumbers
     */
    public static function buildFromInvoice(OrderInvoice $invoice): InvoiceTotalNumbers
    {
        return new self(
            new Number((string) $invoice->total_paid_tax_excl),
            new Number((string) $invoice->total_paid_tax_incl),
            new Number((string) $invoice->total_products),
            new Number((string) $invoice->total_products_wt),
            new Number((string) $invoice->total_wrapping_tax_excl),
            new Number((string) $invoice->total_wrapping_tax_incl),
            new Number((string) $invoice->total_discount_tax_excl),
            new Number((string) $invoice->total_discount_tax_incl),
            new Number((string) $invoice->total_shipping_tax_excl),
            new Number((string) $invoice->total_shipping_tax_incl)
        );
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
