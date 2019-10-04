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
    private $productsPrice;

    /**
     * @var string
     */
    private $discountsAmount;

    /**
     * @var string
     */
    private $wrappingPrice;

    /**
     * @var string
     */
    private $shippingPrice;

    /**
     * @var string
     */
    private $shippingRefundableAmount;

    public function __construct(
        string $productsPrice,
        ?string $discountsAmount,
        ?string $wrappingPrice,
        ?string $shippingPrice,
        ?string $shippingRefundableAmount
    ) {
        $this->productsPrice = $productsPrice;
        $this->discountsAmount = $discountsAmount;
        $this->wrappingPrice = $wrappingPrice;
        $this->shippingPrice = $shippingPrice;
        $this->shippingRefundableAmount = $shippingRefundableAmount;
    }

    /**
     * @return string
     */
    public function getProductsPrice(): string
    {
        return $this->productsPrice;
    }

    /**
     * @return string
     */
    public function getDiscountsAmount(): string
    {
        return $this->discountsAmount;
    }

    /**
     * @return string
     */
    public function getWrappingPrice(): string
    {
        return $this->wrappingPrice;
    }

    /**
     * @return string
     */
    public function getShippingPrice(): string
    {
        return $this->shippingPrice;
    }

    /**
     * @return string
     */
    public function getShippingRefundableAmount(): string
    {
        return $this->shippingRefundableAmount;
    }
}
