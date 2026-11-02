<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\TradeCredit;

use Order;
use OrderSlip;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\IssuePartialRefundCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\VoucherRefundType;
use PrestaShopBundle\Entity\CustomerTradeCreditOperation;

/**
 * Trade credit returned on cancel / refund status and on credit slips.
 */
class TradeCreditRefundFlowTest extends AbstractTradeCreditOrderTestCase
{
    public function testCancelRefundsFullOrderAmount(): void
    {
        $order = $this->validateOrder($this->createCart());

        $this->changeOrderState($order, 'PS_OS_CANCELED');

        $this->assertSame('50000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
        $this->assertSame(CustomerTradeCreditOperation::TYPE_REFUND, $this->repository->findOperation('refund:order:' . $order->id)['type']);
    }

    public function testCancelTwiceDoesNotDoubleRefund(): void
    {
        $order = $this->validateOrder($this->createCart());

        $order = $this->changeOrderState($order, 'PS_OS_CANCELED');
        $order = $this->changeOrderState($order, 'PS_OS_PREPARATION');
        $this->changeOrderState($order, 'PS_OS_CANCELED');

        $this->assertSame('50000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
        $this->assertSame(1, $this->countRefunds((int) $order->id));
    }

    public function testRefundStatusRefunds(): void
    {
        $order = $this->validateOrder($this->createCart());

        $this->changeOrderState($order, 'PS_OS_REFUND');

        $this->assertSame('50000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testPartialRefundReturnsSlipAmount(): void
    {
        $order = $this->paidOrder();
        $balanceBefore = (float) $this->repository->findBalance(self::CUSTOMER_ID);

        $orderSlip = $this->partialRefundOfFirstProduct($order);

        $slipTotal = (float) $orderSlip->total_products_tax_incl + (float) $orderSlip->total_shipping_tax_incl;
        $this->assertGreaterThan(0, $slipTotal);
        $this->assertEqualsWithDelta($balanceBefore + $slipTotal, (float) $this->repository->findBalance(self::CUSTOMER_ID), 0.001);
        $this->assertNotNull($this->repository->findOperation('refund:slip:' . $orderSlip->id));
    }

    public function testCancelAfterPartialRefundReturnsRemainder(): void
    {
        $order = $this->paidOrder();
        $this->partialRefundOfFirstProduct($order);

        $this->changeOrderState(new Order((int) $order->id), 'PS_OS_CANCELED');

        $this->assertSame('50000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testCancelAfterPaymentErrorStateRefunds(): void
    {
        $cart = $this->createCart();
        // A paid amount different from the cart total switches a logable state to "payment error"
        $order = $this->validateOrder($cart, null, 1.0, 'PS_OS_PAYMENT');
        $this->assertSame((int) \Configuration::get('PS_OS_ERROR'), (int) $order->current_state);
        $this->assertNotSame('50000.000000', $this->repository->findBalance(self::CUSTOMER_ID));

        $this->changeOrderState($order, 'PS_OS_CANCELED');

        $this->assertSame('50000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testCancelOrderOfOtherModuleDoesNothing(): void
    {
        $this->paymentModuleRepository->setTradeCreditModules(1, []);
        $order = $this->validateOrder($this->createCart());

        $this->changeOrderState($order, 'PS_OS_CANCELED');

        $this->assertNull($this->repository->findBalance(self::CUSTOMER_ID));
    }

    private function paidOrder(): Order
    {
        $cart = $this->createCart();
        $this->addProduct($cart, self::OTHER_PRODUCT_ID);
        $order = $this->validateOrder($cart);
        $this->assertCount(2, $order->getProducts(), 'The order must contain both cart products.');

        return $this->changeOrderState($order, 'PS_OS_PAYMENT');
    }

    private function partialRefundOfFirstProduct(Order $order): OrderSlip
    {
        $orderDetail = $order->getProducts()[array_key_first($order->getProducts())];

        self::getContainer()->get('prestashop.core.command_bus')->handle(new IssuePartialRefundCommand(
            (int) $order->id,
            [(int) $orderDetail['id_order_detail'] => ['quantity' => 1, 'amount' => (string) $orderDetail['unit_price_tax_incl']]],
            '0',
            false,
            true,
            false,
            VoucherRefundType::PRODUCT_PRICES_EXCLUDING_VOUCHER_REFUND
        ));

        $slips = OrderSlip::getOrdersSlip((int) $order->id_customer, (int) $order->id);
        $this->assertNotEmpty($slips, 'A credit slip must have been created.');

        return new OrderSlip((int) $slips[0]['id_order_slip']);
    }

    private function countRefunds(int $orderId): int
    {
        return (int) self::getContainer()->get('doctrine.dbal.default_connection')->fetchOne(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'customer_trade_credit_operation WHERE type = ? AND id_order = ?',
            [CustomerTradeCreditOperation::TYPE_REFUND, $orderId]
        );
    }
}
