<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\TradeCredit;

use PrestaShop\Decimal\DecimalNumber;

/**
 * Computes how much trade credit can still be returned for an order,
 * so that refunds of an order never exceed what was charged for it.
 */
class TradeCreditRefundCalculator
{
    /**
     * @param DecimalNumber $orderShare amount charged to the credit for the order
     * @param DecimalNumber $alreadyRefunded amount already returned for the order
     * @param DecimalNumber $requested amount the refund event asks to return
     */
    public function refundable(DecimalNumber $orderShare, DecimalNumber $alreadyRefunded, DecimalNumber $requested): DecimalNumber
    {
        $remaining = $orderShare->minus($alreadyRefunded);
        $refundable = $requested->isLowerThan($remaining) ? $requested : $remaining;

        return $refundable->isNegative() ? new DecimalNumber('0') : $refundable;
    }
}
