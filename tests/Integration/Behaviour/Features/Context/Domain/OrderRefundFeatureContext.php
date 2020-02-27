<?php
/**
 * 2007-2020 Friends of PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0).
 * It is also available through the world-wide-web at this URL: https://opensource.org/licenses/AFL-3.0
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Configuration;
use Order;
use OrderSlip;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssueReturnProductCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssueStandardRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidOrderStateException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidRefundException;
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
            $this->lastException = null;
            $command = $this->createIssuePartialRefundCommand(
                $orderId,
                $refundData,
                $restockProducts,
                $generateCreditSlip,
                $generateVoucher
            );

            $this->getCommandBus()->handle($command);
        } catch (OrderException $e) {
            $this->lastException = $e;
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
            $this->lastException = null;
            $command = $this->createIssueStandardRefundCommand(
                $orderId,
                $refundData,
                $generateCreditSlip,
                $generateVoucher
            );

            $this->getCommandBus()->handle($command);
        } catch (OrderException $e) {
            $this->lastException = $e;
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
            $this->lastException = null;
            $command = $this->createIssueReturnProductCommand(
                $orderId,
                $refundData,
                $restockProducts,
                $generateCreditSlip,
                $generateVoucher
            );

            $this->getCommandBus()->handle($command);
        } catch (OrderException $e) {
            $this->lastException = $e;
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
     */
    public function assertLastErrorIsInvalidRefundQuantity()
    {
        $this->assertLastErrorIs(InvalidRefundException::class, InvalidRefundException::INVALID_QUANTITY);
    }

    /**
     * @Then I should get error that refund quantity is too high and max is :maxRefund
     */
    public function assertLastErrorIsRefundQuantityTooHigh(int $maxRefund)
    {
        $this->assertLastErrorIs(InvalidRefundException::class, InvalidRefundException::QUANTITY_TOO_HIGH);
        if ($maxRefund !== $this->lastException->getRefundableQuantity()) {
            throw new RuntimeException(sprintf('Invalid refundable quantity in exception, expected %s but got %s', $maxRefund, $this->lastException->getRefundableQuantity()));
        }
    }

    /**
     * @Then I should get error that refund amount is invalid
     */
    public function assertLastErrorIsInvalidRefundAmount()
    {
        $this->assertLastErrorIs(InvalidRefundException::class, InvalidRefundException::INVALID_AMOUNT);
    }

    /**
     * @Then I should get error that no generation is invalid
     */
    public function assertLastErrorIsInvalidNoGeneration()
    {
        $this->assertLastErrorIs(InvalidRefundException::class, InvalidRefundException::NO_GENERATION);
    }

    /**
     * @Then I should get error that no refunds is invalid
     */
    public function assertLastErrorIsInvalidNoRefunds()
    {
        $this->assertLastErrorIs(InvalidRefundException::class, InvalidRefundException::NO_REFUNDS);
    }

    /**
     * @Then I should get error that return product is disabled
     */
    public function assertLastErrorIsReturnProductDisabled()
    {
        $this->assertLastErrorIs(ReturnProductDisabledException::class);
    }

    /**
     * @Then I should get error that order state is invalid
     */
    public function assertLastErrorIsInvalidOrderState()
    {
        $this->assertLastErrorIs(InvalidOrderStateException::class);
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
     * @throws InvalidRefundException
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
     * @param float|null $voucherRefundAmount
     *
     * @return IssueStandardRefundCommand
     *
     * @throws InvalidRefundException
     * @throws OrderException
     */
    private function createIssueStandardRefundCommand(
        int $orderId,
        array $refunds,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType = VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND,
        ?float $voucherRefundAmount = null
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
            $voucherRefundType,
            $voucherRefundAmount
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
     * @return IssueReturnProductCommand
     *
     * @throws InvalidRefundException
     * @throws OrderException
     */
    private function createIssueReturnProductCommand(
        int $orderId,
        array $refunds,
        bool $restockRefundedProducts,
        bool $generateCreditSlip,
        bool $generateVoucher,
        int $voucherRefundType = VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND,
        ?float $voucherRefundAmount = null
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
            $voucherRefundType,
            $voucherRefundAmount
        );
    }
}
