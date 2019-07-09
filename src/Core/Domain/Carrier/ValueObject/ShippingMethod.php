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

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;

/**
 * Provides valid values for shipping method
 */
final class ShippingMethod
{
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
    private $value;

    /**
     * @param int $value
     *
     * @throws CarrierConstraintException
     */
    public function __construct($value)
    {
        $this->assertValueIsDefinedShippingMethod($value);
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @throws CarrierConstraintException
     */
    private function assertValueIsDefinedShippingMethod($value)
    {
        $definedMethods = [
            self::SHIPPING_METHOD_PRICE,
            self::SHIPPING_METHOD_WEIGHT,
        ];

        if (!in_array($value, $definedMethods, true)) {
            throw new CarrierConstraintException(sprintf(
                'Invalid shipping method "%s". Defined methods are: %s',
                var_export($value, true),
                implode(', ', $definedMethods)),
                CarrierConstraintException::INVALID_SHIPPING_METHOD
            );
        }
    }
}
