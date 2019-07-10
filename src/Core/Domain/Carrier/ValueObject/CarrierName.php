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
 * Provides valid carrier name value
 */
final class CarrierName
{
    /**
     * Allowed carrier name pattern
     */
    const VALID_PATTERN = '/^[^<>;=#{}]*$/u';

    /**
     * Allowed maximum length for carrier name
     */
    const MAX_LENGTH = 64;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     *
     * @throws CarrierConstraintException
     */
    public function __construct($value)
    {
        $this->assertIsValidLengthString($value);
        $this->assertValueMatchesPattern($value);
        $this->value = $value;
    }

    /**
     * @param string $value
     *
     * @throws CarrierConstraintException
     */
    private function assertIsValidLengthString($value)
    {
        if (!is_string($value) || 0 === strlen($value) || self::MAX_LENGTH < strlen($value)) {
            throw new CarrierConstraintException(sprintf(
                'Carrier name "%s" is invalid. It must be 1 - %s characters long string',
                self::MAX_LENGTH,
                var_export($value, true)),
                CarrierConstraintException::INVALID_CARRIER_NAME
            );
        }
    }

    /**
     * @param string $value
     *
     * @throws CarrierConstraintException
     */
    private function assertValueMatchesPattern($value)
    {
        if (!preg_match(self::VALID_PATTERN, $value)) {
            throw new CarrierConstraintException(sprintf(
                'Carrier name "%s" is invalid. It must match "%s" pattern.',
                var_export($value, true),
                self::VALID_PATTERN),
                CarrierConstraintException::INVALID_CARRIER_NAME
            );
        }
    }
}
