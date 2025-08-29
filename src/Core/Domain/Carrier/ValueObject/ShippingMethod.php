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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject;

use PrestaShop\PrestaShop\Core\Domain\Carrier\Exception\CarrierConstraintException;

/**
 * Shipping method value for Carriers
 */
class ShippingMethod
{
    /**
     * Use weight to calculate shipping cost
     */
    public const BY_WEIGHT = 1;

    /**
     * Use price to calculate shipping cost
     */
    public const BY_PRICE = 2;

    /**
     * A list of available values
     */
    public const AVAILABLE_VALUES = [
        self::BY_WEIGHT,
        self::BY_PRICE,
    ];

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
        $this->assertValue($value);
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
    private function assertValue(int $value): void
    {
        if (!in_array($value, self::AVAILABLE_VALUES, true)) {
            throw new CarrierConstraintException(
                sprintf(
                    'Invalid shipping method %s. Valid types are: [%s]',
                    $value,
                    implode(',', self::AVAILABLE_VALUES)
                ),
                CarrierConstraintException::INVALID_SHIPPING_METHOD
            );
        }
    }
}
