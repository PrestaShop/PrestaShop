<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command;

use DateTimeImmutable;
use PrestaShop\PrestaShop\Core\Domain\Exception\DomainConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\ValueObject\Email;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;

/**
 * Submits the online withdrawal statement of a consumer for an order (Directive 2011/83/EU, article 11a).
 *
 * The statement holds what the consumer must be able to provide or confirm: their name, the contract they
 * withdraw from (the order) and the electronic means the acknowledgement of receipt is sent to (the email).
 */
final class SubmitWithdrawalRequestCommand
{
    public const NAME_MAX_LENGTH = 255;
    public const MESSAGE_MAX_LENGTH = 1000;

    private OrderId $orderId;

    private Email $email;

    private DateTimeImmutable $submittedAt;

    /**
     * @throws WithdrawalRequestConstraintException
     */
    public function __construct(
        int $orderId,
        private readonly string $firstname,
        private readonly string $lastname,
        string $email,
        private readonly string $message = '',
        ?DateTimeImmutable $submittedAt = null,
    ) {
        $this->orderId = new OrderId($orderId);
        $this->assertName($firstname, WithdrawalRequestConstraintException::INVALID_FIRSTNAME);
        $this->assertName($lastname, WithdrawalRequestConstraintException::INVALID_LASTNAME);

        try {
            $this->email = new Email($email);
        } catch (DomainConstraintException $e) {
            throw new WithdrawalRequestConstraintException(
                sprintf('Invalid email "%s".', $email),
                WithdrawalRequestConstraintException::INVALID_EMAIL,
                $e
            );
        }

        if (mb_strlen($message) > self::MESSAGE_MAX_LENGTH) {
            throw new WithdrawalRequestConstraintException(
                sprintf('The message cannot exceed %d characters.', self::MESSAGE_MAX_LENGTH),
                WithdrawalRequestConstraintException::INVALID_MESSAGE
            );
        }

        $this->submittedAt = $submittedAt ?? new DateTimeImmutable();
    }

    public function getOrderId(): OrderId
    {
        return $this->orderId;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Date and time the consumer confirmed the withdrawal: it is the legal date of the withdrawal, and it is
     * written on the acknowledgement of receipt.
     */
    public function getSubmittedAt(): DateTimeImmutable
    {
        return $this->submittedAt;
    }

    private function assertName(string $name, int $errorCode): void
    {
        if ('' === trim($name) || mb_strlen($name) > self::NAME_MAX_LENGTH) {
            throw new WithdrawalRequestConstraintException(
                sprintf('Invalid name "%s", it must be a non-empty string of at most %d characters.', $name, self::NAME_MAX_LENGTH),
                $errorCode
            );
        }
    }
}
