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
 * Provides shipping range and its prices by zone
 */
final class ShippingRange
{
    /**
     * @var int
     */
    private $from;

    /**
     * @var int
     */
    private $to;

    /**
     * @var array
     */
    private $pricesByZoneId;

    /**
     * @param int $from
     * @param int $to
     * @param array $pricesByZoneId
     *
     * @throws CarrierConstraintException
     */
    public function __construct($from, $to, array $pricesByZoneId)
    {
        $this->assertRangeIsValid($from, $to);
        $this->assertPricesByZoneArrayIsNotEmpty($pricesByZoneId);
        $this->from = $from;
        $this->to = $to;
        $this->pricesByZoneId = $pricesByZoneId;
    }

    /**
     * @return int
     */
    public function getFrom()
    {
        return $this->from;
    }

    /**
     * @return int
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @return array
     */
    public function getPricesByZoneId()
    {
        return $this->pricesByZoneId;
    }

    /**
     * @param int $from
     * @param int $to
     *
     * @throws CarrierConstraintException
     */
    private function assertRangeIsValid($from, $to)
    {
        $this->assertValueIsNonNegativeInteger($from);
        $this->assertValueIsNonNegativeInteger($to);

        if ($from >= $to) {
            throw new CarrierConstraintException(sprintf(
                'Invalid shipping range "%s - %s". Range to must be higher than range from.', $from, $to),
                CarrierConstraintException::INVALID_SHIPPING_RANGE
            );
        }
    }

    /**
     * @param int $value
     *
     * @throws CarrierConstraintException
     */
    private function assertValueIsNonNegativeInteger($value)
    {
        if (!is_int($value) || 0 > $value) {
            throw new CarrierConstraintException(sprintf(
                'Shipping range "%s" is invalid. It should be non-negative integer.',
                var_export($value, true)),
                CarrierConstraintException::INVALID_SHIPPING_RANGE
            );
        }
    }

    /**
     * @param array $pricesByZone
     *
     * @throws CarrierConstraintException
     */
    private function assertPricesByZoneArrayIsNotEmpty(array $pricesByZone)
    {
        if (empty($pricesByZone)) {
            throw new CarrierConstraintException(sprintf(
                'Shipping prices should be provided at least for one zone in shipping range'),
                CarrierConstraintException::INVALID_SHIPPING_RANGE
            );
        }
    }
}
