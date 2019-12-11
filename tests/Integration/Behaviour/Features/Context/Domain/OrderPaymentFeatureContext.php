<?php

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Order\Payment\Command\AddPaymentCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderPaymentsForViewing;
use PrestaShopException;
use RuntimeException;
use Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class OrderPaymentFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When I add payment to order with id :orderId with the following properties:
     *
     * @param int $orderId
     * @param TableNode $table
     *
     * @throws RuntimeException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function iAddPaymentToWithIdOrderWithIdTheFollowingProperties(int $orderId, TableNode $table)
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
     * @Then if I query order with id :orderId payments I should get :numberOfPayments payments
     *
     * @param int $orderId
     * @param int $numberOfPayments
     *
     * @throws RuntimeException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function ifIQueryOrderWithIdPaymentsIShouldGetPayments(int $orderId, int $numberOfPayments)
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
     * @Then if I query order with id :orderId payments I should get an Order with properties:
     *
     * @param int $orderId
     * @param TableNode $table
     *
     * @throws RuntimeException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function ifIQueryOrderWithIdPaymentsIShouldGetAnOrderWithProperties(int $orderId, TableNode $table)
    {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));
        /** @var OrderPaymentsForViewing $orderPaymentsForViewing */
        $orderPaymentsForViewing = $orderForViewing->getPayments();
        /** @var OrderPaymentForViewing[] $orderPaymentForViewingArray */
        $orderPaymentForViewingArray = $orderPaymentsForViewing->getPayments();

        if (count($orderPaymentForViewingArray) == 0) {
            throw new RuntimeException('Order [' . $orderId . '] has no payments for viewing');
        }

        /** @var OrderPaymentForViewing $orderPaymentForViewing */
        $orderPaymentForViewing = $orderPaymentForViewingArray[0];

        /** @var array $hash */
        $hash = $table->getHash();
        if (count($hash) == 0) {
            throw new RuntimeException('Payment details are invalid');
        }
        /** @var array $data */
        $data = $hash[0];

        $orderPaymentDateFromDb = $orderPaymentForViewing->getDate()->format('Y-m-d H:i:s');
        $orderPaymentDate = isset($data['date']) ? $data['date'] : false;
        if ($orderPaymentDate && $orderPaymentDate !== $orderPaymentDateFromDb) {
            throw new RuntimeException(sprintf(
                'Order "%s" payment date is not the same as "%s", but "%s" was expected',
                $orderId,
                $orderPaymentDateFromDb,
                $orderPaymentDate
            ));
        }

        $paymentMethodFromDb = $orderPaymentForViewing->getPaymentMethod();
        $orderPaymentMethod = isset($data['payment_method']) ? $data['payment_method'] : false;
        if ($orderPaymentMethod && $orderPaymentMethod !== $paymentMethodFromDb) {
            throw new RuntimeException(sprintf(
                'Order "%s" payment method is not the same as "%s", but "%s" was expected',
                $orderId,
                $paymentMethodFromDb,
                $orderPaymentMethod
            ));
        }

        $transactionIdFromDb = $orderPaymentForViewing->getTransactionId();
        $transactionId = isset($data['transaction_id']) ? $data['transaction_id'] : false;
        if ($transactionId && $transactionId !== $transactionIdFromDb) {
            throw new RuntimeException(sprintf(
                'Order "%s" transaction id is not the same as "%s", but "%s" was expected',
                $orderId,
                $transactionIdFromDb,
                $transactionId
            ));
        }

        $amountFromDb = $orderPaymentForViewing->getAmount();
        $amount = isset($data['amount']) ? $data['amount'] : false;
        if ($amount && $amount !== $amountFromDb) {
            throw new RuntimeException(sprintf(
                'Order "%s" amount is not the same as "%s", but "%s" was expected',
                $orderId,
                $amountFromDb,
                $amount
            ));
        }

        $invoiceNumberFromDb = $orderPaymentForViewing->getInvoiceNumber();
        $invoiceId = isset($data['id_invoice']) ? $data['id_invoice'] : false;
        if ($invoiceId && $invoiceId !== $invoiceNumberFromDb && $invoiceNumberFromDb != '') {
            throw new RuntimeException(sprintf(
                'Order "%s" invoice id is not the same as "%s", but "%s" was expected',
                $orderId,
                $invoiceNumberFromDb,
                $invoiceId
            ));
        }
    }

    /**
     * @When I add payment to order with id :orderId exception is thrown with the following properties:
     *
     * @param int $orderId
     * @param TableNode $table
     *
     * @throws RuntimeException
     * @throws ServiceCircularReferenceException
     * @throws ServiceNotFoundException
     */
    public function iAddPaymentToOrderWithIdExceptionIsThrownWithTheFollowingProperties(int $orderId, TableNode $table)
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
}
