<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Order\Refund;

use Configuration;
use Db;
use Mail;
use Order;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderRefundSummary;
use PrestaShop\PrestaShop\Adapter\Order\Refund\OrderSlipCreator;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\InvalidCancelProductException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Refunding free products moves no money, so the refunded amount is zero while products are still going
 * back. Requiring a positive amount before recording anything meant a partial refund of a product priced
 * at zero was refused, and the handler restocks before it gets here, so the refusal arrived after the
 * stock had already moved.
 */
class OrderSlipCreatorFreeProductTest extends KernelTestCase
{
    private OrderSlipCreator $orderSlipCreator;

    private Order $order;

    private string $originalMailMethod;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        // Recording a slip notifies the customer; nothing here is about the email.
        $this->originalMailMethod = (string) Configuration::get('PS_MAIL_METHOD');
        Configuration::updateValue('PS_MAIL_METHOD', Mail::METHOD_DISABLE);

        $orderId = (int) Db::getInstance()->getValue(
            'SELECT id_order FROM ' . _DB_PREFIX_ . 'orders ORDER BY id_order'
        );
        self::assertGreaterThan(0, $orderId, 'the shop needs at least one order');

        $this->order = new Order($orderId);
        $this->orderSlipCreator = self::$kernel->getContainer()->get('prestashop.adapter.order.refund.order_slip_creator');
    }

    protected function tearDown(): void
    {
        Configuration::updateValue('PS_MAIL_METHOD', $this->originalMailMethod);

        Db::getInstance()->execute(
            'DELETE FROM ' . _DB_PREFIX_ . 'order_slip WHERE id_order = ' . (int) $this->order->id
        );

        parent::tearDown();
    }

    /**
     * The guard has to stay for a refund that carries nothing at all.
     */
    public function testARefundOfNothingIsStillRefused(): void
    {
        $this->expectException(InvalidCancelProductException::class);

        $this->orderSlipCreator->create($this->order, $this->summary([], 0.0));
    }

    /**
     * Products going back with no money attached is a real refund and has to be recorded.
     */
    public function testFreeProductsGoingBackAreRecorded(): void
    {
        $before = $this->slipCount();

        $this->orderSlipCreator->create($this->order, $this->summary($this->aFreeProductGoingBack(), 0.0));

        self::assertSame(
            $before + 1,
            $this->slipCount(),
            'a refund of free products was refused, so nothing was recorded for it'
        );
    }

    /**
     * A product that cost money and comes to nothing, here because a voucher is larger than what is
     * refunded, is not a free product going back and has to stay refused.
     *
     * @dataProvider provideRefundedAmountsForAPaidProduct
     */
    public function testAPaidProductRefundedToNothingIsStillRefused(float $refundedAmount): void
    {
        $this->expectException(InvalidCancelProductException::class);

        $this->orderSlipCreator->create($this->order, $this->summary($this->aProductGoingBack(12.0), $refundedAmount));
    }

    public function provideRefundedAmountsForAPaidProduct(): iterable
    {
        yield 'zero' => [0.0];
        yield 'below zero' => [-2.5];
    }

    /**
     * @param array<int, array<string, mixed>> $productRefunds
     */
    private function summary(array $productRefunds, float $refundedAmount): OrderRefundSummary
    {
        return new OrderRefundSummary(
            [],
            $productRefunds,
            $refundedAmount,
            0.0,
            0.0,
            false,
            true,
            2
        );
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function aFreeProductGoingBack(): array
    {
        return $this->aProductGoingBack(0.0);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function aProductGoingBack(float $unitPrice): array
    {
        $orderDetail = Db::getInstance()->getRow(
            'SELECT id_order_detail FROM ' . _DB_PREFIX_ . 'order_detail WHERE id_order = ' . (int) $this->order->id
        );
        self::assertNotEmpty($orderDetail, 'the order needs at least one line');

        // The same shape OrderRefundCalculator produces, so the slip is built from real keys.
        return [
            (int) $orderDetail['id_order_detail'] => [
                'id_order_detail' => (int) $orderDetail['id_order_detail'],
                'quantity' => 1,
                'amount' => 0.0,
                'unit_price' => 0.0,
                'unit_price_tax_excl' => $unitPrice,
                'unit_price_tax_incl' => $unitPrice,
                'total_price_tax_excl' => $unitPrice,
                'total_price_tax_incl' => $unitPrice,
            ],
        ];
    }

    private function slipCount(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'order_slip WHERE id_order = ' . (int) $this->order->id
        );
    }
}
