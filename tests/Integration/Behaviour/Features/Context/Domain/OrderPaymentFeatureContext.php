<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use PHPUnit_Framework_Assert;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command\AddPaymentCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentsForViewing;
use PrestaShopException;
use RuntimeException;

class OrderPaymentFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I pay order :orderId with the following details:
     *
     * @param int $orderId
     * @param TableNode $table
     *
     * @throws RuntimeException
     */
    public function addPaymentToOrderWithTheFollowingDetails(int $orderId, TableNode $table)
    {
        /** @var array $hash */
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Payment details are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        $this->getCommandBus()->handle(
            new AddPaymentCommand(
                $orderId,
                $data['date'],
                $data['payment_method'],
                $data['amount'],
                (int) $data['id_currency'],
                $data['id_invoice'],
                $data['transaction_id']
            )
        );
    }

    /**
     * @Then order :orderId has :numberOfPayments payments
     *
     * @param int $orderId
     * @param int $numberOfPayments
     *
     * @throws RuntimeException
     */
    public function getOrderPayments(int $orderId, int $numberOfPayments)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderPaymentsForViewing $orderPaymentsForViewing */
        $orderPaymentsForViewing = $orderForViewing->getPayments();
        /** @var OrderPaymentForViewing[] $orderPaymentForViewingArray */
        $orderPaymentForViewingArray = $orderPaymentsForViewing->getPayments();

        $countOfOrderPaymentsFromDb = count($orderPaymentForViewingArray);
        if (count($orderPaymentForViewingArray) !== $numberOfPayments) {
            throw new RuntimeException(sprintf(
                'Order "%s" number of payments  is "%s", but "%s" was expected',
                $orderId,
                $countOfOrderPaymentsFromDb,
                $numberOfPayments
            ));
        }
    }

    /**
     * @Then order :orderId payments should have invoice :expectedInvoiceNumber
     *
     * @param int $orderId
     * @param string $expectedInvoiceNumber
     */
    public function queryOrderPaymentsToGetInvoice(int $orderId, string $expectedInvoiceNumber)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        /** @var OrderPaymentForViewing $orderPaymentForViewing */
        $orderPaymentForViewing = $this->getFirstPaymentForViewing($orderId, $orderForViewing);
        $invoiceNumber = $orderPaymentForViewing->getInvoiceNumber();
        PHPUnit_Framework_Assert::assertSame($expectedInvoiceNumber, $invoiceNumber);
    }

    /**
     * @Then order :orderId payments should have the following details:
     *
     * @param int $orderId
     * @param TableNode $table
     */
    public function queryOrderPaymentsToGetTheFollowingProperties(int $orderId, TableNode $table)
    {
        $dataArray = $this->extractFirstRowFromHorizontalTableDetails($table);
        $expectedOrderPaymentForViewing = $this->mapToOrderPaymentForViewing($orderId, $dataArray);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderPaymentForViewing $orderPaymentForViewing */
        $orderPaymentForViewing = $this->getFirstPaymentForViewing($orderId, $orderForViewing);

        PHPUnit_Framework_Assert::assertEquals($expectedOrderPaymentForViewing, $orderPaymentForViewing);
    }

    /**
     * @When I pay order :orderId with the invalid following details:
     *
     * @param int $orderId
     * @param TableNode $table
     *
     * @throws RuntimeException
     */
    public function addPaymentToOrderWithTheInvalidFollowingProperties(int $orderId, TableNode $table)
    {
        /** @var array $hash */
        $hash = $table->getHash();
        if (count($hash) != 1) {
            throw new RuntimeException('Payment details are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        try {
            $this->getCommandBus()->handle(
                new AddPaymentCommand(
                    $orderId,
                    $data['date'],
                    $data['payment_method'],
                    $data['amount'],
                    (int) $data['id_currency'],
                    $data['id_invoice'],
                    $data['transaction_id']
                )
            );
        } catch (PrestaShopException $exception) {
            $msg = $exception->getMessage();
            $expectedMsg = 'Property Order->total_paid_real is not valid';
            if ($msg !== 'Property Order->total_paid_real is not valid') {
                throw new RuntimeException(sprintf(
                    'Not expected exception is thrown "%s" but "%s" was expected',
                    $msg,
                    $expectedMsg));
            }
        }
    }

    private function mapToOrderPaymentForViewing(int $paymentId, array $data)
    {
        return new OrderPaymentForViewing(
            $paymentId,
            new DateTimeImmutable($data['date']),
            $data['payment_method'],
            $data['transaction_id'],
            $data['amount'],
            $data['id_invoice'],
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
