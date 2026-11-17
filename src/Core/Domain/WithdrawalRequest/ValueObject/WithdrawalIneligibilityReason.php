<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject;

/**
 * Why an order cannot be withdrawn from through the withdrawal function.
 */
enum WithdrawalIneligibilityReason: string
{
    /** The withdrawal function is disabled for the shop the order was placed in. */
    case FEATURE_DISABLED = 'feature_disabled';

    /** The order is cancelled or in error: there is no contract left to withdraw from. */
    case ORDER_CANCELLED = 'order_cancelled';

    /** The withdrawal period ended. */
    case PERIOD_EXPIRED = 'period_expired';

    /** A withdrawal statement was already submitted for this order. */
    case ALREADY_REQUESTED = 'already_requested';
}
