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
use Configuration;
use Order;
use OrderSlip;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\CancelOrderProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssueReturnProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssueStandardRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\ReturnProductDisabledException;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderRefundFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @BeforeScenario
     */
    public function before()
    {
        // Merchandise return is disabled by default, use enabledReturnProduct() to enable it
        Configuration::set('PS_ORDER_RETURN', 0);
    }

    /**
     * @When /^I issue a partial refund on "(.*)" (with|without) restock (with|without) credit slip (with|without) voucher on following products:$/
     *
     * @param string $orderReference
     * @param string $restockProducts
     * @param string $generateCreditSlip
     * @param string $generateVoucher
     * @param TableNode $table
     */
    public function issuePartialRefundOrder(
        string $orderReference,
        string $restockProducts,
        string $generateCreditSlip,
        string $generateVoucher,
        TableNode $table
    ) {
        $restockProducts = 'with' === $restockProducts;
        $generateCreditSlip = 'with' === $generateCreditSlip;
        $generateVoucher = 'with' === $generateVoucher;
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $refundData = $table->getColumnsHash();

        try {
            $command = $this->createIssuePartialRefundCommand(
                $orderId,
                $refundData,
                $restockProducts,
                $generateCreditSlip,
                $generateVoucher
            );

            $this->getCommandBus()->handle($command);
        } catch (OrderException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I issue a standard refund on "(.*)" (with|without) credit slip (with|without) voucher on following products:$/
     *
     * @param string $orderReference
     * @param string $generateCreditSlip
     * @param string $generateVoucher
     * @param TableNode $table
     */
    public function issueStandardRefundOrder(
        string $orderReference,
        string $generateCreditSlip,
        string $generateVoucher,
        TableNode $table
    ) {
        $generateCreditSlip = 'with' === $generateCreditSlip;
        $generateVoucher = 'with' === $generateVoucher;
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $refundData = $table->getColumnsHash();

        try {
            $command = $this->createIssueStandardRefundCommand(
                $orderId,
                $refundData,
                $generateCreditSlip,
                $generateVoucher
            );

            $this->getCommandBus()->handle($command);
        } catch (OrderException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When /^I issue a return product on "(.*)" (with|without) restock (with|without) credit slip (with|without) voucher on following products:$/
     *
     * @param string $orderReference
     * @param string $restockProducts
     * @param string $generateCreditSlip
     * @param string $generateVoucher
     * @param TableNode $table
     */
    public function issueReturnProductOrder(
        string $orderReference,
        string $restockProducts,
        string $generateCreditSlip,
        string $generateVoucher,
        TableNode $table
    ) {
        $restockProducts = 'with' === $restockProducts;
        $generateCreditSlip = 'with' === $generateCreditSlip;
        $generateVoucher = 'with' === $generateVoucher;
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $refundData = $table->getColumnsHash();

        try {
            $command = $this->createIssueReturnProductCommand(
                $orderId,
                $refundData,
                $restockProducts,
                $generateCreditSlip,
                $generateVoucher
            );

            $this->getCommandBus()->handle($command);
        } catch (OrderException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Given :orderReference has :creditSlipNumber credit slips
     *
     * @param $orderReference
     * @param int $creditSlipNumber
     */
    public function checkOrderRefundsNumber($orderReference, int $creditSlipNumber)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);

        $order = new Order($orderId);
        $orderSlips = $order->getOrderSlipsCollection();
        if ($creditSlipNumber !== $orderSlips->count()) {
            $errorMessage = sprintf('Invalid number of credit slips on order %s, expected %s but got %s', $orderReference, $creditSlipNumber, $orderSlips->count());
            throw new RuntimeException($errorMessage);
        }
    }

    /**
     * @Given :orderReference last credit slip is:
     *
     * @param $orderReference
     * @param TableNode $table
     */
    public function checkOrderRefunds($orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $refundData = $table->getRowsHash();

        $order = new Order($orderId);
        $orderSlips = $order->getOrderSlipsCollection();
        /** @var OrderSlip $orderSlip */
        $orderSlip = $orderSlips->offsetGet($orderSlips->count() - 1);
        foreach ($refundData as $orderSlipField => $orderSlipValue) {
            Assert::assertEquals(
                (float) $orderSlipValue,
                $orderSlip->{$orderSlipField},
                sprintf(
                    'Invalid order slip field %s, expected %s instead of %s',
                    $orderSlipField,
                    $orderSlipValue,
                    $orderSlip->{$orderSlipField}
                )
            );
        }
    }

    /**
     * @Given return product is enabled
     */
    public function enabledReturnProduct()
    {
        Configuration::set('PS_ORDER_RETURN', 1);
    }

    /**
     * @Then I should get error that refund quantity is invalid
     * @Then I should get error that cancel quantity is invalid
     */
    public function assertLastErrorIsInvalidRefundQuantity()
    {
        $this->assertLastErrorIs(InvalidCancelProductException::class, InvalidCancelProductException::INVALID_QUANTITY);
    }

    /**
     * @Then I should get error that refund quantity is too high and max is :maxRefund
     * @Then I should get error that cancel quantity is too high and max is :maxRefund
     */
    public function assertLastErrorIsRefundQuantityTooHigh(int $maxRefund)
    {
        $this->assertLastErrorIs(
            InvalidCancelProductException::class,
            InvalidCancelProductException::QUANTITY_TOO_HIGH
        );
        if ($maxRefund !== $this->getLastException()->getRefundableQuantity()) {
            throw new RuntimeException(sprintf('Invalid refundable quantity in exception, expected %s but got %s', $maxRefund, $this->getLastException()->getRefundableQuantity()));
        }
    }

    /**
     * @Then I should get error that refund amount is invalid
     */
    public function assertLastErrorIsInvalidRefundAmount()
    {
        $this->assertLastErrorIs(InvalidCancelProductException::class, InvalidCancelProductException::INVALID_AMOUNT);
    }

    /**
     * @Then I should get error that no generation is invalid
     */
    public function assertLastErrorIsInvalidNoGeneration()
    {
        $this->assertLastErrorIs(InvalidCancelProductException::class, InvalidCancelProductException::NO_GENERATION);
    }

    /**
     * @Then I should get error that no refunds is invalid
     */
    public function assertLastErrorIsInvalidNoRefunds()
    {
        $this->assertLastErrorIs(InvalidCancelProductException::class, InvalidCancelProductException::NO_REFUNDS);
    }

    /**
     * @Then I should get error that return product is disabled
     */
    public function assertLastErrorIsReturnProductDisabled()
    {
        $this->assertLastErrorIs(ReturnProductDisabledException::class);
    }

    /**
     * @Then I should get error that order is already paid
     */
    public function assertLastErrorIsOrderIsAlreadyPaid()
    {
        $this->assertLastErrorIs(
            InvalidOrderStateException::class,
            InvalidOrderStateException::ALREADY_PAID
        );
    }

    /**
     * @Then I should get error that order is not paid
     */
    public function assertLastErrorIsOrderIsNotPaid()
    {
        $this->assertLastErrorIs(
            InvalidOrderStateException::class,
            InvalidOrderStateException::NOT_PAID
        );
    }

    /**
     * @Then I should get error that order is delivered
     */
    public function assertLastErrorIsOrderIsDelivered()
    {
        $this->assertLastErrorIs(
            InvalidOrderStateException::class,
            InvalidOrderStateException::UNEXPECTED_DELIVERY
        );
    }

    /**
     * @Then I should get error that order is not delivered
     */
    public function assertLastErrorIsOrderIsNotDelivered()
    {
        $this->assertLastErrorIs(
            InvalidOrderStateException::class,
            InvalidOrderStateException::DELIVERY_NOT_FOUND
        );
    }

    /**
     * @param int $orderId
     * @param array $refunds
     * @param bool $restockRefundedProducts
     * @param bool $generateCreditSlip
     * @param bool $generateVoucher
     * @param int $voucherRefundType
     * @param float|null $voucherRefundAmount
     *
     * @return IssuePartialRefundCommand
     *
     * @throws InvalidCancelProductException
     * @throws OrderException
     */
    private function createIssuePartialRefundCommand(
        int $orderId,
        array $refunds,
        bool $restockRefundedProducts,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType = VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND,
        ?float $voucherRefundAmount = null
    ): IssuePartialRefundCommand {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing((int) $orderId));

        $shippingCostRefund = 0;
        $orderDetailsRefunds = [];
        foreach ($refunds as $refund) {
            if ('shipping_refund' === $refund['product_name']) {
                $shippingCostRefund = $refund['amount'];
                continue;
            }
            $products = $orderForViewing->getProducts()->getProducts();
            /** @var OrderProductForViewing $product */
            foreach ($products as $product) {
                if ($product->getName() === $refund['product_name']) {
                    $orderDetailsRefunds[$product->getOrderDetailId()]['quantity'] = $refund['quantity'];
                    $orderDetailsRefunds[$product->getOrderDetailId()]['amount'] = $refund['amount'];
                    continue 2;
                }
            }

            throw new RuntimeException(sprintf('Product %s not found in orders products', $refund['product_name']));
        }

        return new IssuePartialRefundCommand(
            $orderId,
            $orderDetailsRefunds,
            $shippingCostRefund,
            $restockRefundedProducts,
            $generateCreditSlip,
            $generateVoucher,
            $voucherRefundType,
            $voucherRefundAmount
        );
    }

    /**
     * @param int $orderId
     * @param array $refunds
     * @param bool $generateCreditSlip
     * @param bool $generateVoucher
     * @param int $voucherRefundType
     *
     * @return IssueStandardRefundCommand
     *
     * @throws InvalidCancelProductException
     * @throws OrderException
     */
    private function createIssueStandardRefundCommand(
        int $orderId,
        array $refunds,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType = VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND
    ): IssueStandardRefundCommand {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing((int) $orderId));

        $refundShippingCost = false;
        $orderDetailsRefunds = [];
        foreach ($refunds as $refund) {
            if ('shipping_refund' === $refund['product_name']) {
                $refundShippingCost = (int) $refund['quantity'] > 0;
                continue;
            }
            $products = $orderForViewing->getProducts()->getProducts();
            /** @var OrderProductForViewing $product */
            foreach ($products as $product) {
                if ($product->getName() === $refund['product_name']) {
                    $orderDetailsRefunds[$product->getOrderDetailId()]['quantity'] = $refund['quantity'];
                    continue 2;
                }
            }

            throw new RuntimeException(sprintf('Product %s not found in orders products', $refund['product_name']));
        }

        return new IssueStandardRefundCommand(
            $orderId,
            $orderDetailsRefunds,
            $refundShippingCost,
            $generateCreditSlip,
            $generateVoucher,
            $voucherRefundType
        );
    }

    /**
     * @param int $orderId
     * @param array $refunds
     * @param bool $restockRefundedProducts
     * @param bool $generateCreditSlip
     * @param bool $generateVoucher
     * @param int $voucherRefundType
     *
     * @return IssueReturnProductCommand
     *
     * @throws InvalidCancelProductException
     * @throws OrderException
     */
    private function createIssueReturnProductCommand(
        int $orderId,
        array $refunds,
        bool $restockRefundedProducts,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType = VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND
    ): IssueReturnProductCommand {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing((int) $orderId));

        $refundShippingCost = false;
        $orderDetailsRefunds = [];
        foreach ($refunds as $refund) {
            if ('shipping_refund' === $refund['product_name']) {
                $refundShippingCost = (int) $refund['quantity'] > 0;
                continue;
            }
            $products = $orderForViewing->getProducts()->getProducts();
            /** @var OrderProductForViewing $product */
            foreach ($products as $product) {
                if ($product->getName() === $refund['product_name']) {
                    $orderDetailsRefunds[$product->getOrderDetailId()]['quantity'] = $refund['quantity'];
                    continue 2;
                }
            }

            throw new RuntimeException(sprintf('Product %s not found in orders products', $refund['product_name']));
        }

        return new IssueReturnProductCommand(
            $orderId,
            $orderDetailsRefunds,
            $restockRefundedProducts,
            $refundShippingCost,
            $generateCreditSlip,
            $generateVoucher,
            $voucherRefundType
        );
    }

    /**
     * @When I cancel the following products from order :orderReference:
     *
     * @param string $orderReference
     * @param TableNode $table
     */
    public function cancelOrderProduct(string $orderReference, TableNode $table)
    {
        $cancelProductInfos = $table->getColumnsHash();
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing((int) $orderId));
        $products = $orderForViewing->getProducts()->getProducts();
        $cancelledProducts = [];

        foreach ($cancelProductInfos as $cancelledProductInfo) {
            foreach ($products as $product) {
                if ($product->getName() === $cancelledProductInfo['product_name']) {
                    $cancelledProducts[$product->getOrderDetailId()] = $cancelledProductInfo['quantity'];
                }
            }
        }
        try {
            $command = new CancelOrderProductCommand(
                $cancelledProducts,
                $orderForViewing->getId()
            );

            $this->getCommandBus()->handle($command);
        } catch (OrderException $e) {
            $this->setLastException($e);
        }
    }
}
