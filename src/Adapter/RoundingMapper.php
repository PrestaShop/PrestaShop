<?php
/**
 * 2007-2017 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter;

use PrestaShop\Decimal\Operation\Rounding;

/**
 * Class RoundingMapper
 *
 * Maps rounding modes from legacy rounding modes values to the new PrestaShop/Decimal's Rounding constant values.
 */
final class RoundingMapper
{
    /**
     * Maps rounding modes from legacy rounding modes values to the new PrestaShop/Decimal's Rounding constant values.
     * eg. : asking mapping for PS_ROUND_UP (value : 0) would return Rounding::ROUND_CEIL (value : 'ceil')
     *
     * @param int $legacyRoundingMode
     *   The legacy rounding mode value
     *
     * @return string
     *   The corresponding Rounding class' constant value
     */
    public static function mapRounding($legacyRoundingMode)
    {
        $roundModes = array(
            PS_ROUND_UP        => Rounding::ROUND_CEIL,
            PS_ROUND_DOWN      => Rounding::ROUND_FLOOR,
            PS_ROUND_HALF_UP   => Rounding::ROUND_HALF_UP,
            PS_ROUND_HALF_DOWN => Rounding::ROUND_HALF_DOWN,
            PS_ROUND_HALF_EVEN => Rounding::ROUND_HALF_EVEN,
            PS_ROUND_HALF_ODD  => Rounding::ROUND_HALF_EVEN, // Rounding::ROUND_HALF_ODD does not exist (never used)
        );
        if (!array_key_exists((int)$legacyRoundingMode, $roundModes)) {
            throw new \InvalidArgumentException('Unknown legacy rounding mode : ' . (int)$legacyRoundingMode);
        }

        return $roundModes[$legacyRoundingMode];
    }
}
