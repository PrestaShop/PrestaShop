<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryResult;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalIneligibilityReason;

/**
 * Whether an order can be withdrawn from through the withdrawal function.
 */
final class OrderWithdrawalEligibility
{
    public function __construct(
        private readonly int $orderId,
        private readonly ?WithdrawalIneligibilityReason $ineligibilityReason,
        private readonly ?DateTimeImmutable $deadline,
        private readonly ?int $withdrawalRequestId = null,
        private readonly ?DateTimeImmutable $withdrawalRequestedAt = null,
    ) {
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function isEligible(): bool
    {
        return null === $this->ineligibilityReason;
    }

    public function getIneligibilityReason(): ?WithdrawalIneligibilityReason
    {
        return $this->ineligibilityReason;
    }

    /**
     * End of the withdrawal period, null while the goods have not been delivered (the period has not started).
     */
    public function getDeadline(): ?DateTimeImmutable
    {
        return $this->deadline;
    }

    /**
     * Id of the withdrawal request already submitted for this order, if any.
     */
    public function getWithdrawalRequestId(): ?int
    {
        return $this->withdrawalRequestId;
    }

    public function getWithdrawalRequestedAt(): ?DateTimeImmutable
    {
        return $this->withdrawalRequestedAt;
    }
}
