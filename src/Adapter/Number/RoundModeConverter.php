<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Number;

use PrestaShop\Decimal\Operation\Rounding;
use PrestaShop\PrestaShop\Adapter\CoreException;

class RoundModeConverter
{
    public const MODE_MAP = [
        PS_ROUND_UP => Rounding::ROUND_CEIL,
        PS_ROUND_DOWN => Rounding::ROUND_FLOOR,
        PS_ROUND_HALF_UP => Rounding::ROUND_HALF_UP,
        PS_ROUND_HALF_DOWN => Rounding::ROUND_HALF_DOWN,
        PS_ROUND_HALF_EVEN => Rounding::ROUND_HALF_EVEN,
    ];

    /**
     * @param int $legacyRoundMode
     *
     * @return string
     *
     * @throws CoreException
     */
    public static function getNumberRoundMode(int $legacyRoundMode): string
    {
        if (!isset(static::MODE_MAP[$legacyRoundMode])) {
            throw new CoreException(sprintf('Cannot map round mode %d', $legacyRoundMode));
        }

        return static::MODE_MAP[$legacyRoundMode];
    }
}
