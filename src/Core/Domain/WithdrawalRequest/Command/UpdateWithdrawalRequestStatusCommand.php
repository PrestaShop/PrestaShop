<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command;

use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalRequestId;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalRequestStatus;

/**
 * Updates the processing status of a withdrawal request (merchant follow-up).
 */
final class UpdateWithdrawalRequestStatusCommand
{
    private WithdrawalRequestId $withdrawalRequestId;

    private WithdrawalRequestStatus $status;

    /**
     * @throws WithdrawalRequestConstraintException
     */
    public function __construct(int $withdrawalRequestId, string $status)
    {
        $this->withdrawalRequestId = new WithdrawalRequestId($withdrawalRequestId);

        $withdrawalRequestStatus = WithdrawalRequestStatus::tryFrom($status);
        if (null === $withdrawalRequestStatus) {
            throw new WithdrawalRequestConstraintException(
                sprintf('Invalid withdrawal request status "%s".', $status),
                WithdrawalRequestConstraintException::INVALID_STATUS
            );
        }
        $this->status = $withdrawalRequestStatus;
    }

    public function getWithdrawalRequestId(): WithdrawalRequestId
    {
        return $this->withdrawalRequestId;
    }

    public function getStatus(): WithdrawalRequestStatus
    {
        return $this->status;
    }
}
