<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use Behat\Gherkin\Node\TableNode;
use Order;
use OrderSlip;
use PHPUnit\Framework\Assert as Assert;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\EmptyRefundAmountException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\EmptyRefundQuantityException;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;
use RuntimeException;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderRefundFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When /^I issue a partial refund on "(.*)" (with|without) restock (with|without) voucher on following products:$/
     *
     * @param string $orderReference
     * @param string $restockProducts
     * @param string $generateVoucher
     * @param TableNode $table
     */
    public function issuePartialRefundOrder(string $orderReference, $restockProducts, $generateVoucher, TableNode $table)
    {
        $restockProducts = 'with' === $restockProducts;
        $generateVoucher = 'with' === $generateVoucher;
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $refundData = $table->getColumnsHash();

        try {
            $this->lastException = null;
            $command = $this->createIssuePartialRefundCommand(
                $orderId,
                $refundData,
                $restockProducts,
                $generateVoucher
            );

            $this->getCommandBus()->handle($command);
        } catch (OrderException $e) {
            $this->lastException = $e;
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
     * @Then I should get error that refund quantity is empty
     */
    public function assertLastErrorIsEmptyRefundQuantity()
    {
        $this->assertLastErrorIs(EmptyRefundQuantityException::class);
    }

    /**
     * @Then I should get error that refund amount is empty
     */
    public function assertLastErrorIsEmptyRefundAmount()
    {
        $this->assertLastErrorIs(EmptyRefundAmountException::class);
    }

    /**
     * @param int $orderId
     * @param array $refunds
     * @param bool $restockRefundedProducts
     * @param bool $generateVoucher
     * @param int $voucherRefundType
     * @param float|null $voucherRefundAmount
     *
     * @return IssuePartialRefundCommand
     */
    private function createIssuePartialRefundCommand(
        int $orderId,
        array $refunds,
        bool $restockRefundedProducts,
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
            $generateVoucher,
            $voucherRefundType,
            $voucherRefundAmount
        );
    }
}
