<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Pricing\Rounding;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Operation\Rounding;

/**
 * Reads PS_PRICE_ROUND_MODE from configuration and delegates to DecimalNumber::toPrecision().
 * In Phase 1 the default precision is 0 (round to integers).
 */
class RoundingService implements RoundingServiceInterface
{
    /**
     * Maps PrestaShop PS_PRICE_ROUND_MODE config values to DecimalNumber rounding modes.
     *
     * 0 = Round up away from zero when half way
     * 1 = Round down towards zero when half way
     * 2 = Round towards the next even value
     * 3 = Round up to the nearest value
     * 4 = Round down to the nearest value
     * 5 = Truncate
     */
    protected const ROUNDING_MODE_MAP = [
        0 => Rounding::ROUND_HALF_UP,
        1 => Rounding::ROUND_HALF_DOWN,
        2 => Rounding::ROUND_HALF_EVEN,
        3 => Rounding::ROUND_CEIL,
        4 => Rounding::ROUND_FLOOR,
        5 => Rounding::ROUND_TRUNCATE,
    ];

    protected readonly string $roundingMode;

    public function __construct(
        int $legacyRoundMode = 0,
    ) {
        $this->roundingMode = self::ROUNDING_MODE_MAP[$legacyRoundMode] ?? Rounding::ROUND_HALF_UP;
    }

    public function round(DecimalNumber $value, ?int $precision = null): DecimalNumber
    {
        // Phase 1: default precision is 0 (round to integers)
        $precision = $precision ?? 0;

        return new DecimalNumber($value->toPrecision($precision, $this->roundingMode));
    }
}
