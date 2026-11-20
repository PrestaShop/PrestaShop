<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use DateTimeImmutable;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\UpdateOrderStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\SubmitWithdrawalRequestCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Command\UpdateWithdrawalRequestStatusCommand;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\OrderNotEligibleForWithdrawalException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestConstraintException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Exception\WithdrawalRequestNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Query\GetOrderWithdrawalEligibility;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\Query\GetWithdrawalRequestForViewing;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryResult\OrderWithdrawalEligibility;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\QueryResult\WithdrawalRequestForViewing;
use PrestaShop\PrestaShop\Core\Domain\WithdrawalRequest\ValueObject\WithdrawalIneligibilityReason;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\CommonFeatureContext;

class WithdrawalRequestFeatureContext extends AbstractDomainFeatureContext
{
    private const DATE_FORMAT = 'Y-m-d H:i:s';

    private const CONSTRAINT_ERROR_CODES = [
        'id' => WithdrawalRequestConstraintException::INVALID_ID,
        'firstname' => WithdrawalRequestConstraintException::INVALID_FIRSTNAME,
        'lastname' => WithdrawalRequestConstraintException::INVALID_LASTNAME,
        'email' => WithdrawalRequestConstraintException::INVALID_EMAIL,
        'message' => WithdrawalRequestConstraintException::INVALID_MESSAGE,
        'status' => WithdrawalRequestConstraintException::INVALID_STATUS,
    ];

    /**
     * The delivered state is set through the command bus, then dated: the withdrawal period depends on the
     * delivery date, scenarios need it to be fixed.
     *
     * @Given order :orderReference was delivered on :date
     */
    public function orderWasDeliveredOn(string $orderReference, string $date): void
    {
        $orderId = $this->referenceToId($orderReference);
        $deliveredStateId = (int) Configuration::get('PS_OS_DELIVERED');

        $this->getCommandBus()->handle(new UpdateOrderStatusCommand($orderId, $deliveredStateId));

        CommonFeatureContext::getContainer()->get('doctrine.dbal.default_connection')->executeStatement(
            'UPDATE `' . _DB_PREFIX_ . 'order_history` SET `date_add` = :date WHERE `id_order` = :orderId AND `id_order_state` = :stateId',
            ['date' => $date, 'orderId' => $orderId, 'stateId' => $deliveredStateId]
        );
    }

    /**
     * @Then the withdrawal eligibility of order :orderReference on :date should be:
     */
    public function assertWithdrawalEligibility(string $orderReference, string $date, TableNode $table): void
    {
        $expected = $table->getRowsHash();

        /** @var OrderWithdrawalEligibility $eligibility */
        $eligibility = $this->getQueryBus()->handle(
            new GetOrderWithdrawalEligibility($this->referenceToId($orderReference), new DateTimeImmutable($date))
        );

        if (isset($expected['eligible'])) {
            Assert::assertSame(
                $this->toBool($expected['eligible']),
                $eligibility->isEligible(),
                sprintf('Unexpected eligibility of order "%s"', $orderReference)
            );
        }
        if (array_key_exists('reason', $expected)) {
            Assert::assertSame(
                $expected['reason'],
                (string) $eligibility->getIneligibilityReason()?->value,
                sprintf('Unexpected ineligibility reason of order "%s"', $orderReference)
            );
        }
        if (array_key_exists('deadline', $expected)) {
            Assert::assertSame(
                $expected['deadline'],
                (string) $eligibility->getDeadline()?->format(self::DATE_FORMAT),
                sprintf('Unexpected withdrawal deadline of order "%s"', $orderReference)
            );
        }
    }

    /**
     * @When I withdraw from order :orderReference on :date with request :requestReference and the following statement:
     */
    public function withdrawFromOrder(string $orderReference, string $date, string $requestReference, TableNode $table): void
    {
        $withdrawalRequestId = $this->getCommandBus()->handle(
            $this->buildSubmitCommand($orderReference, $date, $table->getRowsHash())
        );

        $this->getSharedStorage()->set($requestReference, $withdrawalRequestId->getValue());
    }

    /**
     * @When I try to withdraw from order :orderReference on :date with the following statement:
     */
    public function tryToWithdrawFromOrder(string $orderReference, string $date, TableNode $table): void
    {
        try {
            $this->getCommandBus()->handle($this->buildSubmitCommand($orderReference, $date, $table->getRowsHash()));
        } catch (OrderNotEligibleForWithdrawalException|WithdrawalRequestConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I mark withdrawal request :requestReference as :status
     */
    public function markWithdrawalRequestAs(string $requestReference, string $status): void
    {
        $this->getCommandBus()->handle(
            new UpdateWithdrawalRequestStatusCommand($this->referenceToId($requestReference), $status)
        );
    }

    /**
     * @When I try to mark withdrawal request :requestReference as :status
     */
    public function tryToMarkWithdrawalRequestAs(string $requestReference, string $status): void
    {
        try {
            $this->markWithdrawalRequestAs($requestReference, $status);
        } catch (WithdrawalRequestConstraintException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I try to mark the unknown withdrawal request :withdrawalRequestId as :status
     */
    public function tryToMarkUnknownWithdrawalRequestAs(int $withdrawalRequestId, string $status): void
    {
        try {
            $this->getCommandBus()->handle(new UpdateWithdrawalRequestStatusCommand($withdrawalRequestId, $status));
        } catch (WithdrawalRequestNotFoundException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then withdrawal request :requestReference should have the following properties:
     */
    public function assertWithdrawalRequestProperties(string $requestReference, TableNode $table): void
    {
        /** @var WithdrawalRequestForViewing $withdrawalRequest */
        $withdrawalRequest = $this->getQueryBus()->handle(
            new GetWithdrawalRequestForViewing($this->referenceToId($requestReference))
        );

        $actual = [
            'order' => $withdrawalRequest->getOrderId(),
            'firstname' => $withdrawalRequest->getFirstname(),
            'lastname' => $withdrawalRequest->getLastname(),
            'email' => $withdrawalRequest->getEmail(),
            'message' => $withdrawalRequest->getMessage(),
            'status' => $withdrawalRequest->getStatus(),
            'submitted at' => $withdrawalRequest->getSubmittedAt()->format(self::DATE_FORMAT),
        ];

        foreach ($table->getRowsHash() as $property => $expectedValue) {
            if (!array_key_exists($property, $actual)) {
                throw new RuntimeException(sprintf('Unknown withdrawal request property "%s"', $property));
            }
            if ('order' === $property) {
                $expectedValue = $this->referenceToId($expectedValue);
            }

            Assert::assertSame(
                $expectedValue,
                $actual[$property],
                sprintf('Unexpected "%s" of withdrawal request "%s"', $property, $requestReference)
            );
        }
    }

    /**
     * @Then I should get an error that the order is not eligible for withdrawal because :reason
     */
    public function assertLastErrorIsNotEligible(string $reason): void
    {
        /** @var OrderNotEligibleForWithdrawalException $exception */
        $exception = $this->assertLastErrorIs(OrderNotEligibleForWithdrawalException::class);

        Assert::assertSame(WithdrawalIneligibilityReason::from($reason), $exception->getReason());
    }

    /**
     * @Then I should get an error that the withdrawal request :field is invalid
     */
    public function assertLastErrorIsInvalidField(string $field): void
    {
        if (!isset(self::CONSTRAINT_ERROR_CODES[$field])) {
            throw new RuntimeException(sprintf('Unknown withdrawal request field "%s"', $field));
        }

        $this->assertLastErrorIs(WithdrawalRequestConstraintException::class, self::CONSTRAINT_ERROR_CODES[$field]);
    }

    /**
     * @Then I should get an error that the withdrawal request was not found
     */
    public function assertLastErrorIsNotFound(): void
    {
        $this->assertLastErrorIs(WithdrawalRequestNotFoundException::class);
    }

    /**
     * @param array<string, string> $statement
     */
    private function buildSubmitCommand(string $orderReference, string $date, array $statement): SubmitWithdrawalRequestCommand
    {
        return new SubmitWithdrawalRequestCommand(
            $this->referenceToId($orderReference),
            $statement['firstname'] ?? '',
            $statement['lastname'] ?? '',
            $statement['email'] ?? '',
            $statement['message'] ?? '',
            new DateTimeImmutable($date)
        );
    }

    private function toBool(string $value): bool
    {
        return in_array(strtolower($value), ['true', '1', 'yes'], true);
    }
}
