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
 * Provides valid carrier shipping delay value
 */
class ShippingDelay
{
    /**
     * Regex pattern for valid shipping delay value
     */
    const VALID_PATTERN = '/^[^<>={}]*$/u';

    /**
     * Max allowed length of delay value
     */
    const MAX_ALLOWED_LENGTH = 512;

    /**
     * @var string
     */
    private $value;

    /**
     * @param string $value
     *
     * @throws CarrierConstraintException
     */
    public function __construct(string $value)
    {
        $this->assertIsValidLength($value);
        $this->assertValueMatchesPattern($value);
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @throws CarrierConstraintException
     */
    private function assertIsValidLength(string $value)
    {
        if (self::MAX_ALLOWED_LENGTH < strlen($value)) {
            throw new CarrierConstraintException(sprintf(
                'Carrier shipping delay "%s" is invalid. It must be 1 - %s characters long.',
                $value,
                self::MAX_ALLOWED_LENGTH),
                CarrierConstraintException::INVALID_SHIPPING_DELAY
            );
        }
    }

    /**
     * @param string $value
     *
     * @throws CarrierConstraintException
     */
    private function assertValueMatchesPattern(string $value)
    {
        if (!preg_match('/^[^<>={}]*$/u', $value)) {
            throw new CarrierConstraintException(sprintf(
                'Carrier shipping delay "%s" is invalid. It must match "%s" pattern',
                $value,
                self::VALID_PATTERN
            ));
        }
    }
}
