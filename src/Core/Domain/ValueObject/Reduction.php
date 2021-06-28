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

namespace PrestaShop\PrestaShop\Core\Domain\ValueObject;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;

/**
 * Provides valid reduction values
 */
class Reduction
{
    /**
     * For reducing concrete amount of money from price
     */
    public const TYPE_AMOUNT = 'amount';

    /**
     * For reducing certain percentage calculated from price
     */
    public const TYPE_PERCENTAGE = 'percentage';

    /**
     * Allowed reduction types
     */
    public const ALLOWED_TYPES = [
        self::TYPE_AMOUNT,
        self::TYPE_PERCENTAGE,
    ];

    /**
     * Maximum allowed value for percentage type reduction
     */
    public const MAX_ALLOWED_PERCENTAGE = 100;

    /**
     * @var string
     */
    private $type;

    /**
     * @var DecimalNumber
     */
    private $value;

    /**
     * @param string $type
     * @param string $value
     *
     * @throws DomainConstraintException
     */
    public function __construct(string $type, string $value)
    {
        $this->assertIsAllowedType($type);
        $decimalValue = new DecimalNumber($value);
        $this->assertIsValidValue($type, $decimalValue);

        $this->type = $type;
        $this->value = $decimalValue;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return DecimalNumber
     */
    public function getValue(): DecimalNumber
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
        if (!in_array($type, self::ALLOWED_TYPES, true)) {
            throw new DomainConstraintException(sprintf('The reduction type "%s" is invalid. Valid types are: "%s", "%s".', $type, self::TYPE_AMOUNT, self::TYPE_PERCENTAGE), DomainConstraintException::INVALID_REDUCTION_TYPE);
        }
    }

    /**
     * @param string $type
     * @param DecimalNumber $value
     *
     * @throws DomainConstraintException
     */
    private function assertIsValidValue(string $type, DecimalNumber $value)
    {
        if (self::TYPE_PERCENTAGE === $type) {
            if ($value->isLowerThanZero() || $value->isGreaterThan(new DecimalNumber((string) self::MAX_ALLOWED_PERCENTAGE))) {
                throw new DomainConstraintException(sprintf('Invalid reduction percentage "%s". It must be from 0 to %s%%', (string) $value, self::MAX_ALLOWED_PERCENTAGE), DomainConstraintException::INVALID_REDUCTION_PERCENTAGE);
            }
        }

        if ($value->isLowerThanZero()) {
            throw new DomainConstraintException(sprintf('Invalid reduction amount "%s". It cannot be less than 0', (string) $value), DomainConstraintException::INVALID_REDUCTION_AMOUNT);
        }
    }
}
