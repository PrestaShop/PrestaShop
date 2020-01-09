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

use PHPUnit_Framework_Assert;
use Order;
use Behat\Gherkin\Node\TableNode;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\OrderDocumentType;
use PrestaShop\PrestaShop\Core\Domain\Order\Query\GetOrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderDocumentForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\QueryResult\OrderProductForViewing;
use PrestaShop\PrestaShop\Core\Domain\Order\ValueObject\OrderId;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;
use Tests\Integration\Behaviour\Features\Context\SharedStorage;

class OrderRefundFeatureContext extends AbstractDomainFeatureContext
{
    /**
     * @When /^I issue a partial refund on "(.*)" (with|without) restock on following products:$/
     *
     * @param string $orderReference
     * @param bool $restockProducts
     * @param TableNode $table
     */
    public function issuePartialRefundOrder(string $orderReference, $restockProducts, TableNode $table)
    {
        $restockProducts = 'with' === $restockProducts;
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $refundData = $table->getColumnsHash();

        $command = $this->createIssuePartialRefundCommand(
            $orderId,
            $refundData,
            false,
            $restockProducts,
            false
        );

        $this->getCommandBus()->handle($command);
    }

    /**
     * @Given :orderReference has following refunds:
     *
     * @param $orderReference
     * @param TableNode $table
     */
    public function checkOrderRefunds($orderReference, TableNode $table)
    {
        $orderId = SharedStorage::getStorage()->get($orderReference);
        $refundData = $table->getRowsHash();
        $refundData['amount'] = 'null' === $refundData['amount'] ? null : $refundData['amount'];
        $refundData['shipping'] = 'null' === $refundData['shipping'] ? null : $refundData['shipping'];

        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing((int) $orderId));
        /** @var OrderDocumentForViewing $documentForViewing */
        foreach ($orderForViewing->getDocuments()->getDocuments() as $documentForViewing) {
            switch ($documentForViewing->getType()) {
                case OrderDocumentType::CREDIT_SLIP:
                    PHPUnit_Framework_Assert::assertEquals($documentForViewing->getNumericalAmount(), $refundData['amount']);
                    break;
                case OrderDocumentType::DELIVERY_SLIP:
                    PHPUnit_Framework_Assert::assertEquals($documentForViewing->getNumericalAmount(), $refundData['shipping']);
                    break;
            }
        }
    }

    /**
     * @param int $orderId
     * @param array $refunds
     * @param bool $shippingCostRefund
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
        bool $shippingCostRefund,
        bool $restockRefundedProducts,
        bool $generateVoucher,
        int $voucherRefundType = VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND,
        ?float $voucherRefundAmount = null
    ): IssuePartialRefundCommand {
        /** @var OrderForViewing $orderForViewing */
        $orderForViewing = $this->getQueryBus()->handle(new GetOrderForViewing((int) $orderId));

        $orderDetailsRefunds = [];
        /** @var OrderProductForViewing $product */
        foreach ($orderForViewing->getProducts()->getProducts() as $product) {
            $productRefund = null;
            foreach ($refunds as $refund) {
                if ($refund['product_name'] == $product->getName()) {
                    $productRefund = $refund;
                    break;
                }
            }
            if (null !== $productRefund) {
                $orderDetailsRefunds[$product->getOrderDetailId()]['quantity'] = $productRefund['quantity'];
                $orderDetailsRefunds[$product->getOrderDetailId()]['amount'] = $productRefund['amount'];
            }
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
