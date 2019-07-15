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
 * Provides valid out of range behavior values
 */
final class OutOfRangeBehavior
{
    /**
     * When out of range, the carrier is disabled
     */
    const DISABLE_CARRIER = 0;

    /**
     * When out of range, the shipping costs applies according to to highest defined range
     */
    const APPLY_HIGHEST_RANGE = 1;

    /**
     * @var int
     */
    private $value;

    /**
     * @param int $value
     *
     * @throws CarrierConstraintException
     */
    public function __construct(int $value)
    {
        $this->assertIsDefinedBehavior($value);
        $this->value = $value;
    }

    /**
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * @param int $value
     *
     * @throws CarrierConstraintException
     */
    private function assertIsDefinedBehavior(int $value)
    {
        $definedValues = [
            self::DISABLE_CARRIER,
            self::APPLY_HIGHEST_RANGE,
        ];

        if (!in_array($value, $definedValues, true)) {
            throw new CarrierConstraintException(sprintf(
                'Invalid out of range behavior value "%s". Defined values are: %s',
                $value,
                implode(', ', $definedValues)),
                CarrierConstraintException::INVALID_OUT_OF_RANGE_BEHAVIOR
            );
        }
    }
}
