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

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;

/**
 * Provides valid reduction values
 */
class Reduction
{
    /**
     * For reducing concrete amount of money from price
     */
    const TYPE_AMOUNT = 'amount';

    /**
     * For reducing certain percentage calculated from price
     */
    const TYPE_PERCENTAGE = 'percentage';

    /**
     * Maximum allowed value for percentage type reduction
     */
    const MAX_ALLOWED_PERCENTAGE = 100;

    /**
     * @var string
     */
    private $type;

    /**
     * @var float
     */
    private $value;

    /**
     * @param string $type
     * @param float $value
     *
     * @throws DomainConstraintException
     */
    public function __construct(string $type, float $value)
    {
        $this->assertIsAllowedType($type);
        $this->assertIsValidValue($type, $value);
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getValue(): float
    {
        return $this->value;
    }

    /**
     * @param string $type
     *
     * @throws DomainConstraintException
     */
    private function assertIsAllowedType(string $type)
    {
        if (!in_array($type, [self::TYPE_PERCENTAGE, self::TYPE_AMOUNT], true)) {
            throw new DomainConstraintException(sprintf(
                'The reduction type "%s" is invalid. Valid types are: "%s", "%s".',
                $type,
                    self::TYPE_AMOUNT,
                    self::TYPE_PERCENTAGE
                ),
                DomainConstraintException::INVALID_REDUCTION_TYPE
            );
        }
    }

    /**
     * @param $type
     * @param $value
     *
     * @throws DomainConstraintException
     */
    private function assertIsValidValue(string $type, float $value)
    {
        if (self::TYPE_PERCENTAGE === $type) {
            if (!$this->assertIsNotNegative($value) || self::MAX_ALLOWED_PERCENTAGE < $value) {
                throw new DomainConstraintException(sprintf(
                    'Invalid reduction percentage "%s". It must be from 0 to %s%%',
                    $value,
                    self::MAX_ALLOWED_PERCENTAGE),
                    DomainConstraintException::INVALID_REDUCTION_PERCENTAGE
                );
            }
        }

        if (!$this->assertIsNotNegative($value)) {
            throw new DomainConstraintException(sprintf(
                'Invalid reduction amount "%s". It cannot be less than 0',
                $value),
                DomainConstraintException::INVALID_REDUCTION_AMOUNT
            );
        }
    }

    /**
     * @param float $value
     *
     * @return bool
     */
    private function assertIsNotNegative(float $value)
    {
        return 0 <= $value;
    }
}
