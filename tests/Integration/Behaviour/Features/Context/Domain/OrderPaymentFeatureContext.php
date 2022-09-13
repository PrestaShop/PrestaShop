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
use Context;
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
                (int) Context::getContext()->employee->id,
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

        $orderPaymentForViewing = $this->getPaymentForViewing($orderId, $orderForViewing, 'first');
        $invoiceNumber = $orderPaymentForViewing->getInvoiceNumber();
        Assert::assertNotNull($invoiceNumber);
    }

    /**
     * @Then order :orderReference payment in :position position should have the following details:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function checkOrderPayment(string $orderReference, string $position, TableNode $table): void
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing($orderId));

        $orderPaymentForViewing = $this->getPaymentForViewing($orderId, $orderForViewing, $position);

        $dataArray = $table->getRowsHash();

        if (isset($dataArray['date'])) {
            Assert::assertEquals(
                $dataArray['date'],
                $orderPaymentForViewing->getDate()->format('Y-m-d H:i:s')
            );

            unset($dataArray['date']);
        }

        if (isset($dataArray['employee'])) {
            Assert::assertEquals(
                $dataArray['employee'],
                $orderPaymentForViewing->getEmployeeName()
            );

            unset($dataArray['employee']);
        }

        foreach ($dataArray as $key => $value) {
            Assert::assertEquals(
                $value,
                $orderPaymentForViewing->{'get' . ucfirst($key)}()
            );
        }
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
                    (int) Context::getContext()->employee->id,
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
     * @param string $position
     *
     * @return OrderPaymentForViewing
     *
     * @throws RuntimeException
     */
    private function getPaymentForViewing(int $orderId, OrderForViewing $orderForViewing, string $position): OrderPaymentForViewing
    {
        /** @var OrderPaymentsForViewing $orderPaymentsForViewing */
        $orderPaymentsForViewing = $orderForViewing->getPayments();
        /** @var OrderPaymentForViewing[] $orderPaymentForViewingArray */
        $orderPaymentForViewingArray = $orderPaymentsForViewing->getPayments();
        if (count($orderPaymentForViewingArray) == 0) {
            throw new RuntimeException('Order [' . $orderId . '] has no payments for viewing');
        }

        $indexes = [
            'first' => 0,
            'second' => 1,
            'third' => 2,
            'fourth' => 3,
            'last' => count($orderPaymentForViewingArray) - 1,
        ];

        return $orderPaymentForViewingArray[$indexes[$position] ?? $position];
    }
}
