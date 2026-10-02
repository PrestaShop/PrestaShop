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
     * The keys are the PS_ROUND_* constants from config/defines.inc.php, which is also what the
     * Shop Parameters form stores:
     *
     * PS_ROUND_UP        = 0
     * PS_ROUND_DOWN      = 1
     * PS_ROUND_HALF_UP   = 2
     * PS_ROUND_HALF_DOWN = 3
     * PS_ROUND_HALF_EVEN = 4
     * PS_ROUND_HALF_ODD  = 5 - deliberately absent, see below
     *
     * PS_ROUND_HALF_ODD has no equivalent in the Decimal library, so it falls through to the
     * default in the constructor. Tools::ps_round() does implement it, so a shop using that mode
     * still rounds differently here; that mismatch is tracked in issue #30441.
     */
    protected const ROUNDING_MODE_MAP = [
        PS_ROUND_UP => Rounding::ROUND_CEIL,
        PS_ROUND_DOWN => Rounding::ROUND_FLOOR,
        PS_ROUND_HALF_UP => Rounding::ROUND_HALF_UP,
        PS_ROUND_HALF_DOWN => Rounding::ROUND_HALF_DOWN,
        PS_ROUND_HALF_EVEN => Rounding::ROUND_HALF_EVEN,
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
