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
                (int) $data['id_currency'],
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
            throw new RuntimeException(sprintf(
                'Order "%s" number of payments  is "%s", but "%s" was expected',
                $orderReference,
                $countOfOrderPaymentsFromDb,
                $numberOfPayments
            ));
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
        PHPUnit_Framework_Assert::assertNotNull($invoiceNumber);
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

        PHPUnit_Framework_Assert::assertEquals($expectedOrderPaymentForViewing, $orderPaymentForViewing);
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
                    (int) $data['id_currency'],
                    isset($data['id_invoice']) ? (int) $data['id_invoice'] : null,
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
