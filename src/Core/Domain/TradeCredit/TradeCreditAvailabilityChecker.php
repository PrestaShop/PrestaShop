<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit;

use PrestaShop\Decimal\DecimalNumber;
use PrestaShop\Decimal\Operation\Rounding;

/**
 * Single rule deciding whether an amount can be charged to a customer trade credit.
 * Used both to present the payment option in checkout and to validate the order.
 */
class TradeCreditAvailabilityChecker
{
    public const PRECISION = 2;

    /**
     * @param DecimalNumber|null $balance null when the credit has not been granted yet
     * @param DecimalNumber $requiredAmount amount in the trade credit currency
     */
    public function check(
        ?DecimalNumber $balance,
        bool $isGuest,
        DecimalNumber $defaultAmount,
        DecimalNumber $requiredAmount
    ): TradeCreditAvailability {
        $available = $balance ?? $defaultAmount;
        $required = new DecimalNumber($requiredAmount->round(self::PRECISION, Rounding::ROUND_HALF_UP));

        $reason = null;
        if ($isGuest) {
            $reason = TradeCreditAvailability::REASON_GUEST;
        } elseif ($required->isGreaterThan($available)) {
            $reason = TradeCreditAvailability::REASON_INSUFFICIENT;
        }

        return new TradeCreditAvailability($available, $required, $reason);
    }
}
