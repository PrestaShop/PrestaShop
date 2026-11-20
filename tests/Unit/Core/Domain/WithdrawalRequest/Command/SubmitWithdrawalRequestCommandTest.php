<?php

/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Core\Domain\WithdrawalRequest\Command;

use DateTimeImmutable;
use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\SubmitWithdrawalRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\UpdateWithdrawalRequestStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalRequestStatus;

class SubmitWithdrawalRequestCommandTest extends TestCase
{
    public function testConstructWithValidStatementKeepsValues(): void
    {
        $submittedAt = new DateTimeImmutable('2026-11-10 14:32:10');
        $command = new SubmitWithdrawalRequestCommand(42, 'John', 'Doe', 'pub@prestashop.com', 'Too small', $submittedAt);

        $this->assertSame(42, $command->getOrderId()->getValue());
        $this->assertSame('John', $command->getFirstname());
        $this->assertSame('Doe', $command->getLastname());
        $this->assertSame('pub@prestashop.com', $command->getEmail()->getValue());
        $this->assertSame('Too small', $command->getMessage());
        $this->assertSame($submittedAt, $command->getSubmittedAt());
    }

    public function testConstructWithoutSubmissionDateUsesCurrentDate(): void
    {
        $before = new DateTimeImmutable();
        $command = new SubmitWithdrawalRequestCommand(42, 'John', 'Doe', 'pub@prestashop.com');

        $this->assertGreaterThanOrEqual($before, $command->getSubmittedAt());
        $this->assertSame('', $command->getMessage());
    }

    /**
     * @dataProvider invalidStatementProvider
     */
    public function testConstructWithInvalidStatementThrowsConstraintException(
        string $firstname,
        string $lastname,
        string $email,
        string $message,
        int $expectedCode
    ): void {
        $this->expectException(WithdrawalRequestConstraintException::class);
        $this->expectExceptionCode($expectedCode);

        new SubmitWithdrawalRequestCommand(42, $firstname, $lastname, $email, $message);
    }

    public static function invalidStatementProvider(): Generator
    {
        yield 'empty firstname' => ['', 'Doe', 'pub@prestashop.com', '', WithdrawalRequestConstraintException::INVALID_FIRSTNAME];
        yield 'blank firstname' => ['   ', 'Doe', 'pub@prestashop.com', '', WithdrawalRequestConstraintException::INVALID_FIRSTNAME];
        yield 'empty lastname' => ['John', '', 'pub@prestashop.com', '', WithdrawalRequestConstraintException::INVALID_LASTNAME];
        yield 'too long lastname' => ['John', str_repeat('a', 256), 'pub@prestashop.com', '', WithdrawalRequestConstraintException::INVALID_LASTNAME];
        yield 'empty email' => ['John', 'Doe', '', '', WithdrawalRequestConstraintException::INVALID_EMAIL];
        yield 'too long message' => ['John', 'Doe', 'pub@prestashop.com', str_repeat('a', 1001), WithdrawalRequestConstraintException::INVALID_MESSAGE];
    }

    public function testUpdateStatusCommandWithKnownStatusBuildsEnum(): void
    {
        $command = new UpdateWithdrawalRequestStatusCommand(3, 'processed');

        $this->assertSame(3, $command->getWithdrawalRequestId()->getValue());
        $this->assertSame(WithdrawalRequestStatus::PROCESSED, $command->getStatus());
    }

    public function testUpdateStatusCommandWithUnknownStatusThrowsConstraintException(): void
    {
        $this->expectException(WithdrawalRequestConstraintException::class);
        $this->expectExceptionCode(WithdrawalRequestConstraintException::INVALID_STATUS);

        new UpdateWithdrawalRequestStatusCommand(3, 'archived');
    }

    public function testUpdateStatusCommandWithInvalidIdThrowsConstraintException(): void
    {
        $this->expectException(WithdrawalRequestConstraintException::class);
        $this->expectExceptionCode(WithdrawalRequestConstraintException::INVALID_ID);

        new UpdateWithdrawalRequestStatusCommand(0, 'processed');
    }
}
