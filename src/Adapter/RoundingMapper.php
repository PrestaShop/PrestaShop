<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter;

use InvalidArgumentException;
use PrestaShop\Decimal\Operation\Rounding;

/**
 * Maps rounding modes from legacy rounding modes values to the new PrestaShop/Decimal's Rounding constant values.
 */
final class RoundingMapper
{
    /**
     * Maps rounding modes from legacy rounding modes values to the new PrestaShop/Decimal's Rounding constant values.
     * eg. : asking mapping for PS_ROUND_UP (value : 0) would return Rounding::ROUND_CEIL (value : 'ceil').
     *
     * @param int $legacyRoundingMode
     *                                The legacy rounding mode value
     *
     * @return string
     *                The corresponding Rounding class' constant value
     */
    public static function mapRounding($legacyRoundingMode)
    {
        $roundModes = [
            PS_ROUND_UP => Rounding::ROUND_CEIL,
            PS_ROUND_DOWN => Rounding::ROUND_FLOOR,
            PS_ROUND_HALF_UP => Rounding::ROUND_HALF_UP,
            PS_ROUND_HALF_DOWN => Rounding::ROUND_HALF_DOWN,
            PS_ROUND_HALF_EVEN => Rounding::ROUND_HALF_EVEN,
            PS_ROUND_HALF_ODD => Rounding::ROUND_HALF_EVEN, // Rounding::ROUND_HALF_ODD does not exist (never used)
        ];
        if (!array_key_exists((int) $legacyRoundingMode, $roundModes)) {
            throw new InvalidArgumentException('Unknown legacy rounding mode : ' . (int) $legacyRoundingMode);
        }

        return $roundModes[$legacyRoundingMode];
    }
}
