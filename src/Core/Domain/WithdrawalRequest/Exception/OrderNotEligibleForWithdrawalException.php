<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception;

use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalIneligibilityReason;
use Throwable;

/**
 * Thrown when a withdrawal statement is submitted for an order the consumer cannot withdraw from anymore.
 */
final class OrderNotEligibleForWithdrawalException extends WithdrawalRequestException
{
    public function __construct(
        private readonly WithdrawalIneligibilityReason $reason,
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getReason(): WithdrawalIneligibilityReason
    {
        return $this->reason;
    }
}
