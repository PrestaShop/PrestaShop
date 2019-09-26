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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

/**
 * DTO for order preview data
 */
class OrderPreview
{
    /**
     * @var InvoiceDetails
     */
    private $invoiceDetails;

    /**
     * @var ShippingDetails
     */
    private $shippingDetails;

    /**
     * @var ProductDetail[]
     */
    private $productDetails;
    /**
     * @var bool
     */
    private $taxIncluded;

    /**
     * @param InvoiceDetails $invoiceDetails
     * @param ShippingDetails $shippingDetails
     * @param array $productDetails
     * @param bool $taxIncluded
     */
    public function __construct(
        InvoiceDetails $invoiceDetails,
        ShippingDetails $shippingDetails,
        array $productDetails,
        bool $taxIncluded
    ) {
        $this->invoiceDetails = $invoiceDetails;
        $this->shippingDetails = $shippingDetails;
        $this->productDetails = $productDetails;
        $this->taxIncluded = $taxIncluded;
    }

    /**
     * @return InvoiceDetails
     */
    public function getInvoiceDetails(): InvoiceDetails
    {
        return $this->invoiceDetails;
    }

    /**
     * @return ShippingDetails
     */
    public function getShippingDetails(): ShippingDetails
    {
        return $this->shippingDetails;
    }

    /**
     * @return ProductDetail[]
     */
    public function getProductDetails(): array
    {
        return $this->productDetails;
    }

    /**
     * @return bool
     */
    public function isTaxIncluded(): bool
    {
        return $this->taxIncluded;
    }
}
