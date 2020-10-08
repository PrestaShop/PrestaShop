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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

use PrestaShop\Decimal\DecimalNumber;

class OrderPricesForViewing
{
    /**
     * @var string
     */
    private $productsPriceFormatted;

    /**
     * @var string
     */
    private $discountsAmountFormatted;

    /**
     * @var string
     */
    private $wrappingPriceFormatted;

    /**
     * @var string
     */
    private $shippingPriceFormatted;

    /**
     * @var string
     */
    private $shippingRefundableAmountFormatted;

    /**
     * @var string
     */
    private $taxesAmountFormatted;

    /**
     * @var string
     */
    private $totalAmountFormatted;

    /**
     * @var DecimalNumber
     */
    private $productsPriceRaw;

    /**
     * @var DecimalNumber
     */
    private $discountsAmountRaw;
    /**
     * @var DecimalNumber
     */
    private $wrappingPriceRaw;

    /**
     * @var DecimalNumber
     */
    private $shippingPriceRaw;

    /**
     * @var DecimalNumber
     */
    private $shippingRefundableAmountRaw;

    /**
     * @var DecimalNumber
     */
    private $taxesAmountRaw;

    /**
     * @var DecimalNumber
     */
    private $totalAmountRaw;

    public function __construct(
        DecimalNumber $productsPriceRaw,
        DecimalNumber $discountsAmountRaw,
        DecimalNumber $wrappingPriceRaw,
        DecimalNumber $shippingPriceRaw,
        DecimalNumber $shippingRefundableAmountRaw,
        DecimalNumber $taxesAmountRaw,
        DecimalNumber $totalAmountRaw,
        string $productsPrice,
        string $discountsAmount,
        string $wrappingPrice,
        string $shippingPrice,
        string $shippingRefundableAmount,
        string $taxesAmount,
        string $totalAmount
    ) {
        $this->productsPriceFormatted = $productsPrice;
        $this->discountsAmountFormatted = $discountsAmount;
        $this->wrappingPriceFormatted = $wrappingPrice;
        $this->shippingPriceFormatted = $shippingPrice;
        $this->shippingRefundableAmountFormatted = $shippingRefundableAmount;
        $this->taxesAmountFormatted = $taxesAmount;
        $this->totalAmountFormatted = $totalAmount;
        $this->productsPriceRaw = $productsPriceRaw;
        $this->discountsAmountRaw = $discountsAmountRaw;
        $this->wrappingPriceRaw = $wrappingPriceRaw;
        $this->shippingPriceRaw = $shippingPriceRaw;
        $this->shippingRefundableAmountRaw = $shippingRefundableAmountRaw;
        $this->taxesAmountRaw = $taxesAmountRaw;
        $this->totalAmountRaw = $totalAmountRaw;
    }

    /**
     * @return string
     */
    public function getProductsPriceFormatted(): string
    {
        return $this->productsPriceFormatted;
    }

    /**
     * @return string
     */
    public function getDiscountsAmountFormatted(): ?string
    {
        return $this->discountsAmountFormatted;
    }

    /**
     * @return string
     */
    public function getWrappingPriceFormatted(): ?string
    {
        return $this->wrappingPriceFormatted;
    }

    /**
     * @return string
     */
    public function getShippingPriceFormatted(): ?string
    {
        return $this->shippingPriceFormatted;
    }

    /**
     * @return string
     */
    public function getShippingRefundableAmountFormatted(): ?string
    {
        return $this->shippingRefundableAmountFormatted;
    }

    /**
     * @return string
     */
    public function getTaxesAmountFormatted(): string
    {
        return $this->taxesAmountFormatted;
    }

    /**
     * @return string
     */
    public function getTotalAmountFormatted(): string
    {
        return $this->totalAmountFormatted;
    }

    /**
     * @return DecimalNumber
     */
    public function getProductsPriceRaw(): DecimalNumber
    {
        return $this->productsPriceRaw;
    }

    /**
     * @return DecimalNumber
     */
    public function getDiscountsAmountRaw(): DecimalNumber
    {
        return $this->discountsAmountRaw;
    }

    /**
     * @return DecimalNumber
     */
    public function getWrappingPriceRaw(): DecimalNumber
    {
        return $this->wrappingPriceRaw;
    }

    /**
     * @return DecimalNumber
     */
    public function getShippingPriceRaw(): DecimalNumber
    {
        return $this->shippingPriceRaw;
    }

    /**
     * @return DecimalNumber
     */
    public function getShippingRefundableAmountRaw(): DecimalNumber
    {
        return $this->shippingRefundableAmountRaw;
    }

    /**
     * @return DecimalNumber
     */
    public function getTaxesAmountRaw(): DecimalNumber
    {
        return $this->taxesAmountRaw;
    }

    /**
     * @return DecimalNumber
     */
    public function getTotalAmountRaw(): DecimalNumber
    {
        return $this->totalAmountRaw;
    }
}
