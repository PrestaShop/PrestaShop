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

use PrestaShop\PrestaShop\Core\Domain\CatalogPriceRule\Exception\CatalogPriceRuleConstraintException;

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
     */
    public function __construct($type, $value)
    {
        $this->assertIsAllowedType($type);
        $this->assertIsNotNegativeNumber($value);
        $this->assertIsValidPercentage($type, $value);
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $type
     */
    private function assertIsAllowedType($type)
    {
        if ($type !== self::TYPE_AMOUNT && $type !== self::TYPE_PERCENTAGE) {
            throw new CatalogPriceRuleConstraintException(sprintf(
                'The reduction type "%s" is invalid. Valid types are: "%s", "%s".',
                var_export($type, true),
                    self::TYPE_AMOUNT,
                    self::TYPE_PERCENTAGE
                ),
                CatalogPriceRuleConstraintException::INVALID_REDUCTION_TYPE
            );
        }
    }

    /**
     * @param $value
     */
    private function assertIsNotNegativeNumber($value)
    {
        if (!is_numeric($value) || 0 > $value) {
            throw new CatalogPriceRuleConstraintException(sprintf(
                'Invalid reduction type "%s". It must be positive number',
                var_export($value, true)),
                CatalogPriceRuleConstraintException::INVALID_REDUCTION_VALUE
            );
        }
    }

    /**
     * @param $type
     * @param $value
     */
    private function assertIsValidPercentage($type, $value)
    {
        if (self::TYPE_PERCENTAGE === $type && self::MAX_ALLOWED_PERCENTAGE < $value) {
            throw new CatalogPriceRuleConstraintException(sprintf(
                'Invalid reduction percentage value "%s". Maximum allowed is %s%%',
                $value,
                self::MAX_ALLOWED_PERCENTAGE),
                CatalogPriceRuleConstraintException::INVALID_REDUCTION_VALUE
            );
        }
    }
}
