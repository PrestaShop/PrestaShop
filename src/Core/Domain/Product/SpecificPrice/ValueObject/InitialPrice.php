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

namespace PrestaShop\PrestaShop\Core\Domain\Product\SpecificPrice\ValueObject;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Represents "leave initial product price" option
 */
class InitialPrice implements FixedPriceInterface
{
    /**
     * Inherited from legacy.
     * When SpecificPrice->price has this value, it means that product initial price is used.
     */
    public const INITIAL_PRICE_VALUE = '-1';

    /**
     * @var DecimalNumber
     */
    private $value;

    public function __construct()
    {
        $this->value = new DecimalNumber(self::INITIAL_PRICE_VALUE);
    }

    /**
     * @return DecimalNumber
     */
    public function getValue(): DecimalNumber
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function isInitialPriceValue(string $value): bool
    {
        $initialPrice = new DecimalNumber(self::INITIAL_PRICE_VALUE);

        return $initialPrice->equals(new DecimalNumber($value));
    }
}
