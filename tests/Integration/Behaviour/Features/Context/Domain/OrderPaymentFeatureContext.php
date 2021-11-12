<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\NegativePaymentAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command\AddPaymentCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentsForViewing;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderPaymentFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I pay order :orderReference with the following details:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function addPaymentToOrderWithTheFollowingDetails(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $data = $table->getRowsHash();

        $this->getCommandBus()->handle(
            new AddPaymentCommand(
                $orderId,
                $data['date'],
                $data['payment_method'],
                $data['amount'],
                SharedStorage::getStorage()->get($data['currency']),
                isset($data['id_invoice']) ? (int) $data['id_invoice'] : null,
                $data['transaction_id']
            )
        );
    }

    /**
     * @Then order :orderReference has :numberOfPayments payments
     *
     * @param string $orderReference
     * @param int $numberOfPayments
     */
    public function getOrderPayments(string $orderReference, int $numberOfPayments)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderPaymentsForViewing $orderPaymentsForViewing */
        $orderPaymentsForViewing = $orderForViewing->getPayments();
        /** @var OrderPaymentForViewing[] $orderPaymentForViewingArray */
        $orderPaymentForViewingArray = $orderPaymentsForViewing->getPayments();

        $countOfOrderPaymentsFromDb = count($orderPaymentForViewingArray);
        if (count($orderPaymentForViewingArray) !== $numberOfPayments) {
            throw new RuntimeException(sprintf('Order "%s" number of payments  is "%s", but "%s" was expected', $orderReference, $countOfOrderPaymentsFromDb, $numberOfPayments));
        }
    }

    /**
     * @Then order :orderReference payments should have invoice
     *
     * @param string $orderReference
     */
    public function queryOrderPaymentsToGetInvoice(string $orderReference)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        /** @var OrderPaymentForViewing $orderPaymentForViewing */
        $orderPaymentForViewing = $this->getFirstPaymentForViewing($orderId, $orderForViewing);
        $invoiceNumber = $orderPaymentForViewing->getInvoiceNumber();
        Assert::assertNotNull($invoiceNumber);
    }

    /**
     * @Then order :orderReference payments should have the following details:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function queryOrderPaymentsToGetTheFollowingProperties(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderPaymentForViewing $orderPaymentForViewing */
        $orderPaymentForViewing = $this->getFirstPaymentForViewing($orderId, $orderForViewing);

        $dataArray = $table->getRowsHash();
        $expectedOrderPaymentForViewing = $this->mapToOrderPaymentForViewing(
            $orderPaymentForViewing->getPaymentId(), $dataArray
        );

        Assert::assertEquals($expectedOrderPaymentForViewing, $orderPaymentForViewing);
    }

    /**
     * @When I pay order :orderReference with the invalid following details:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function addPaymentToOrderWithTheInvalidFollowingProperties(string $orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $data = $table->getRowsHash();

        try {
            $this->getCommandBus()->handle(
                new AddPaymentCommand(
                    $orderId,
                    $data['date'],
                    $data['payment_method'],
                    $data['amount'],
                    SharedStorage::getStorage()->get($data['currency']),
                    isset($data['id_invoice']) ? (int) $data['id_invoice'] : null,
                    $data['transaction_id']
                )
            );
        } catch (NegativePaymentAmountException $exception) {
            $this->setLastException($exception);
        } catch (OrderConstraintException $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * @Then I should get error that payment amount is negative
     */
    public function assertLastErrorIsNegativePaymentAmount(): void
    {
        $this->assertLastErrorIs(NegativePaymentAmountException::class);
    }

    /**
     * @Then I should get error that payment method is invalid
     */
    public function assertLastErrorIsInvalidPaymentMethod(): void
    {
        $this->assertLastErrorIs(
            OrderConstraintException::class,
            OrderConstraintException::INVALID_PAYMENT_METHOD
        );
    }

    private function mapToOrderPaymentForViewing(int $paymentId, array $data)
    {
        return new OrderPaymentForViewing(
            $paymentId,
            new DateTimeImmutable($data['date']),
            $data['payment_method'],
            $data['transaction_id'],
            $data['amount'],
            isset($data['id_invoice']) ? (int) $data['id_invoice'] : null,
            '',
            '',
            '',
            ''
        );
    }

    /**
     * @param TableNode $table
     *
     * @return array
     *
     * @throws RuntimeException
     */
    private function extractFirstRowFromHorizontalTableDetails(TableNode $table)
    {
        /** @var array $hash */
        $hash = $table->getHash();
        if (count($hash) == 0) {
            throw new RuntimeException('Payment details are invalid');
        }

        return $hash[0];
    }

    /**
     * @param int $orderId
     * @param OrderForViewing $orderForViewing
     *
     * @return OrderPaymentForViewing
     *
     * @throws RuntimeException
     */
    private function getFirstPaymentForViewing(int $orderId, OrderForViewing $orderForViewing): OrderPaymentForViewing
    {
        /** @var OrderPaymentsForViewing $orderPaymentsForViewing */
        $orderPaymentsForViewing = $orderForViewing->getPayments();
        /** @var OrderPaymentForViewing[] $orderPaymentForViewingArray */
        $orderPaymentForViewingArray = $orderPaymentsForViewing->getPayments();
        if (count($orderPaymentForViewingArray) == 0) {
            throw new RuntimeException('Order [' . $orderId . '] has no payments for viewing');
        }
        /** @var OrderPaymentForViewing $orderPaymentForViewing */
        $orderPaymentForViewing = $orderPaymentForViewingArray[0];

        return $orderPaymentForViewing;
    }
}
