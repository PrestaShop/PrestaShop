<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryResult;

use DateTimeImmutable;

final class WithdrawalRequestForViewing
{
    public function __construct(
        private readonly int $withdrawalRequestId,
        private readonly int $shopId,
        private readonly int $orderId,
        private readonly int $customerId,
        private readonly string $orderReference,
        private readonly string $firstname,
        private readonly string $lastname,
        private readonly string $email,
        private readonly string $message,
        private readonly string $status,
        private readonly DateTimeImmutable $submittedAt,
        private readonly ?DateTimeImmutable $acknowledgedAt,
    ) {
    }

    public function getWithdrawalRequestId(): int
    {
        return $this->withdrawalRequestId;
    }

    public function getShopId(): int
    {
        return $this->shopId;
    }

    public function getOrderId(): int
    {
        return $this->orderId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getOrderReference(): string
    {
        return $this->orderReference;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getSubmittedAt(): DateTimeImmutable
    {
        return $this->submittedAt;
    }

    public function getAcknowledgedAt(): ?DateTimeImmutable
    {
        return $this->acknowledgedAt;
    }
}
