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

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

/**
 * Provides valid values for shipping range
 */
final class ShippingPrice
{
    /**
     * Default shipping method is only used for backwards compatibility.
     *
     * @deprecated 1.5.5
     */
    const SHIPPING_METHOD_DEFAULT = 0;

    /**
     * Represents shipping method when the shipping price depends from total package weight
     */
    const SHIPPING_METHOD_WEIGHT = 1;

    /**
     * Represents shipping method when the shipping price depends from total package price
     */
    const SHIPPING_METHOD_PRICE = 2;

    /**
     * Represents shipping method when the shipping is free of charge
     */
    const SHIPPING_METHOD_FREE = 3;

    /**
     * @var int
     */
    private $shippingMethod;

    /**
     * @var ShippingRange
     */
    private $shippingRange;

    /**
     * @param int $shippingMethod
     * @param array $rangeZonePrices
     */
    public function __construct($shippingMethod, array $rangeZonePrices)
    {
        $this->shippingMethod = $shippingMethod;
        foreach ($rangeZonePrices as $range => $zonePrices) {

        }
    }

    /**
     * @return int
     */
    public function getShippingMethod()
    {
        return $this->shippingMethod;
    }

    /**
     * @return ShippingRange
     */
    public function getShippingRanges()
    {
        return $this->shippingRange;
    }

}
