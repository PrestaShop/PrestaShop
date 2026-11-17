<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject;

/**
 * Processing status of a withdrawal request, as handled by the merchant.
 *
 * The withdrawal itself is a right of the consumer and takes effect when the statement is submitted:
 * these statuses only track the merchant follow-up (refund, return of the goods), or the dispute of a
 * request that falls under an exception (custom-made goods, sealed goods unsealed, etc.).
 */
enum WithdrawalRequestStatus: string
{
    case PENDING = 'pending';
    case PROCESSED = 'processed';
    case REFUSED = 'refused';
}
