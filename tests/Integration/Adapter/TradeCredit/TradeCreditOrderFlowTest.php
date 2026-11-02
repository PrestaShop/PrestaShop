<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\TradeCredit;

use Cart;
use Configuration;
use Order;
use PrestaShop\PrestaShop\Core\Domain\Order\Command\AddOrderFromBackOfficeCommand;
use PrestaShop\PrestaShop\Core\Domain\Order\Exception\OrderException;
use PrestaShop\PrestaShop\Core\Domain\TradeCredit\Exception\TradeCreditInsufficientException;

/**
 * Trade credit debit when validating orders.
 */
class TradeCreditOrderFlowTest extends AbstractTradeCreditOrderTestCase
{
    public function testOrderWithTradeCreditModuleDebitsTotalWithShipping(): void
    {
        $cart = $this->createCart();

        $order = $this->validateOrder($cart);

        $this->assertGreaterThan(0, (float) $order->total_shipping_tax_incl, 'The test carrier must charge shipping.');
        $expected = number_format(50000 - (float) $order->total_paid_tax_incl, 6, '.', '');
        $this->assertSame($expected, $this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testOrderWithOtherModuleDoesNotTouchCredit(): void
    {
        $this->paymentModuleRepository->setTradeCreditModules(1, []);

        $this->validateOrder($this->createCart());

        $this->assertNull($this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testFirstOrderGrantsLazily(): void
    {
        $cart = $this->createCart();
        $this->assertNull($this->repository->findBalance(self::CUSTOMER_ID));

        $this->validateOrder($cart);

        $this->assertNotNull($this->repository->findOperation('grant:customer:' . self::CUSTOMER_ID));
        $this->assertNotNull($this->repository->findOperation('debit:cart:' . $cart->id . ':1'));
    }

    public function testExceedingCreditRejectsOrder(): void
    {
        $this->repository->createBalance(self::CUSTOMER_ID, '10.00');
        $cart = $this->createCart();

        try {
            $this->validateOrder($cart);
            $this->fail('The order above the available trade credit must be rejected.');
        } catch (TradeCreditInsufficientException $e) {
            $this->assertStringContainsString('exceeds your available trade credit', $e->getMessage());
        }

        $this->assertFalse(Order::getIdByCartId((int) $cart->id));
        $this->assertSame('10.000000', $this->repository->findBalance(self::CUSTOMER_ID));
    }

    public function testReserveUsesCartTotalAtValidationTime(): void
    {
        $cart = $this->createCart();
        $totalShownInCheckout = (float) $cart->getOrderTotal(true, Cart::BOTH);
        // The cart changes after the payment step was displayed (e.g. in another tab)
        $this->addProduct($cart, self::OTHER_PRODUCT_ID);
        $totalAtValidation = (float) $cart->getOrderTotal(true, Cart::BOTH);
        $this->assertGreaterThan($totalShownInCheckout, $totalAtValidation);

        $order = $this->validateOrder($cart);

        $debit = $this->repository->findOperation('debit:cart:' . $cart->id . ':1');
        $this->assertSame(number_format(-$totalAtValidation, 6, '.', ''), $debit['amount']);
        $this->assertSame(number_format($totalAtValidation, 6, '.', ''), $order->total_paid_tax_incl);
    }

    public function testOrderCreationFailureReleasesCredit(): void
    {
        $cart = $this->createCart();

        $this->failOrderCreation($cart);

        $this->assertSame('50000.000000', $this->repository->findBalance(self::CUSTOMER_ID));
        $this->assertNotNull($this->repository->findOperation('release:cart:' . $cart->id . ':1'));
    }

    public function testRetryAfterFailedOrderCreationChargesAgain(): void
    {
        $cart = $this->createCart();
        $this->failOrderCreation($cart);

        // The customer retries with the same cart, the order is created this time
        $order = $this->validateOrder($cart);

        $expected = number_format(50000 - (float) $order->total_paid_tax_incl, 6, '.', '');
        $this->assertSame($expected, $this->repository->findBalance(self::CUSTOMER_ID));
        $this->assertNotNull($this->repository->findOperation('debit:cart:' . $cart->id . ':2'));
    }

    public function testBackOfficeOrderWithoutCreditFails(): void
    {
        $this->repository->createBalance(self::CUSTOMER_ID, '10.00');
        $cart = $this->createCart();

        try {
            self::getContainer()->get('prestashop.core.command_bus')->handle(new AddOrderFromBackOfficeCommand(
                (int) $cart->id,
                1,
                '',
                self::PAYMENT_MODULE,
                (int) Configuration::get('PS_OS_BANKWIRE')
            ));
            $this->fail('The back office order above the available trade credit must be rejected.');
        } catch (OrderException $e) {
            $this->assertStringContainsString('exceeds your available trade credit', $e->getMessage());
        }

        $this->assertFalse(Order::getIdByCartId((int) $cart->id));
    }
}
