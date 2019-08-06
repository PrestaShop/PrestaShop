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
 * Provides valid values for carrier speed grade
 */
class SpeedGrade
{
    /**
     * Minimum allowed value for speed grade
     */
    const MIN_VALUE = 0;

    /**
     * Maximum allowed value for speed grade
     */
    const MAX_VALUE = 9;

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
        $this->assertValueIsNonNegativeIntegerLessThanTen($value);
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
     * @param $value
     *
     * @throws CarrierConstraintException
     */
    private function assertValueIsNonNegativeIntegerLessThanTen(int $value)
    {
        if (self::MIN_VALUE > $value || self::MAX_VALUE < $value) {
            throw new CarrierConstraintException(sprintf(
                    'Shipping grade "%s" is invalid. It must be integer from %s to %s',
                    $value,
                    self::MIN_VALUE,
                    self::MAX_VALUE
                ),
                CarrierConstraintException::INVALID_SPEED_GRADE
            );
        }
    }
}
