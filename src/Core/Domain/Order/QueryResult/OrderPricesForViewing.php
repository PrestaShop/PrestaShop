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

namespace PrestaShop\PrestaShop\Core\Domain\Order\QueryResult;

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
     * @var float
     */
    private $productsPriceRaw;

    /**
     * @var float
     */
    private $discountsAmountRaw;
    /**
     * @var float
     */
    private $wrappingPriceRaw;

    /**
     * @var float
     */
    private $shippingPriceRaw;

    /**
     * @var float
     */
    private $shippingRefundableAmountRaw;

    /**
     * @var float
     */
    private $taxesAmountRaw;

    /**
     * @var float
     */
    private $totalAmountRaw;

    public function __construct(
        float $productsPriceRaw,
        float $discountsAmountRaw,
        float $wrappingPriceRaw,
        float $shippingPriceRaw,
        float $shippingRefundableAmountRaw,
        float $taxesAmountRaw,
        float $totalAmountRaw,
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
     * @return float
     */
    public function getProductsPriceRaw(): float
    {
        return $this->productsPriceRaw;
    }

    /**
     * @return float
     */
    public function getDiscountsAmountRaw(): float
    {
        return $this->discountsAmountRaw;
    }

    /**
     * @return float
     */
    public function getWrappingPriceRaw(): float
    {
        return $this->wrappingPriceRaw;
    }

    /**
     * @return float
     */
    public function getShippingPriceRaw(): float
    {
        return $this->shippingPriceRaw;
    }

    /**
     * @return float
     */
    public function getShippingRefundableAmountRaw(): float
    {
        return $this->shippingRefundableAmountRaw;
    }

    /**
     * @return float
     */
    public function getTaxesAmountRaw(): float
    {
        return $this->taxesAmountRaw;
    }

    /**
     * @return float
     */
    public function getTotalAmountRaw(): float
    {
        return $this->totalAmountRaw;
    }
}
