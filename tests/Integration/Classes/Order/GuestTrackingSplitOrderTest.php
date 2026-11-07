<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Customer;
use Db;
use Order;
use PHPUnit\Framework\TestCase;
use Tests\Integration\Utility\ContextMockerTrait;
use Tools;

/**
 * A cart split over several carriers produces one order per package, all sharing a reference. The
 * confirmation emails tell them apart with the #N suffix from Order::getUniqReference(), so whatever that
 * method prints has to lead back to the same order on the guest tracking page.
 *
 * The suffix is not an ordinal: getUniqReference() computes id_order + 1 - MIN(id_order of the cart), so
 * an unrelated order created in between leaves a gap and the numbers skip.
 */
class GuestTrackingSplitOrderTest extends TestCase
{
    use ContextMockerTrait;

    private string $reference;

    private Customer $customer;

    /** @var int[] */
    private array $orderIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        // Unique per test: a stale row from another test must never join this test's set.
        $this->reference = 'SPLIT' . strtoupper(substr(md5(uniqid('', true)), 0, 4));

        $this->customer = new Customer();
        $this->customer->firstname = 'Guest';
        $this->customer->lastname = 'Tracking';
        $this->customer->email = 'guest-tracking-' . uniqid() . '@example.com';
        $this->customer->passwd = Tools::hash('guest-tracking-test');
        $this->customer->add();
    }

    protected function tearDown(): void
    {
        if ($this->orderIds) {
            Db::getInstance()->execute(
                'DELETE FROM ' . _DB_PREFIX_ . 'orders WHERE id_order IN (' . implode(',', $this->orderIds) . ')'
            );
        }
        $this->orderIds = [];
        $this->customer->delete();

        parent::tearDown();
    }

    /**
     * Every order of a split cart must be reachable, not just the first one.
     */
    public function testEachOrderOfASplitCartIsReachableByItsOwnReference(): void
    {
        $cartId = $this->nextCartId();
        $orderIds = [
            $this->createOrder($cartId),
            $this->createOrder($cartId),
            $this->createOrder($cartId),
        ];

        $reached = [];
        foreach ($orderIds as $orderId) {
            $reached[] = $this->resolve((new Order($orderId))->getUniqReference());
        }

        self::assertSame($orderIds, $reached, 'the guest tracking page did not reach every order of the cart');
    }

    /**
     * The gap case. An unrelated order between two orders of the same cart makes getUniqReference() skip a
     * number, so a lookup that counts rows instead of reversing the arithmetic lands on the wrong order.
     */
    public function testItFollowsTheSuffixEvenWhenTheOrderIdsAreNotContiguous(): void
    {
        $cartId = $this->nextCartId();
        $first = $this->createOrder($cartId);
        $this->createOrder($this->nextCartId());       // someone else checking out in between
        $third = $this->createOrder($cartId);

        $suffixedReference = (new Order($third))->getUniqReference();

        self::assertSame(
            $this->reference . '#' . ($third + 1 - $first),
            $suffixedReference,
            'the suffix should be an offset from the first order of the cart'
        );
        self::assertNotSame($this->reference . '#2', $suffixedReference, 'the ids should not be contiguous here');

        self::assertSame($third, $this->resolve($suffixedReference));
    }

    /**
     * A cart that produced a single order has no suffix, and that must keep working unchanged.
     */
    public function testAReferenceWithoutASuffixStillResolves(): void
    {
        $orderId = $this->createOrder($this->nextCartId());

        self::assertSame($this->reference, (new Order($orderId))->getUniqReference());
        self::assertSame($orderId, $this->resolve($this->reference));
    }

    /**
     * A number nobody issued must not quietly return somebody's order.
     */
    public function testAnUnknownSuffixResolvesToNothing(): void
    {
        $cartId = $this->nextCartId();
        $this->createOrder($cartId);
        $this->createOrder($cartId);

        self::assertSame(0, $this->resolve($this->reference . '#99'));
    }

    /**
     * Resolves a reference the way GuestTrackingController does.
     */
    private function resolve(string $uniqueReference): int
    {
        $parts = explode('#', $uniqueReference);
        $number = isset($parts[1]) && ctype_digit(trim($parts[1])) ? (int) trim($parts[1]) : null;

        return (int) Order::getByReferenceAndEmail(current($parts), $this->customer->email, $number)->id;
    }

    /**
     * No ps_cart rows are created here, so MAX(id_cart) alone would hand the same id to two tests in one
     * run. Take the orders table into account as well, and step past the ids already used.
     */
    private function nextCartId(): int
    {
        $highestCart = (int) Db::getInstance()->getValue('SELECT MAX(id_cart) FROM ' . _DB_PREFIX_ . 'cart');
        $highestUsed = (int) Db::getInstance()->getValue('SELECT MAX(id_cart) FROM ' . _DB_PREFIX_ . 'orders');

        return 1 + max($highestCart, $highestUsed) + count($this->orderIds);
    }

    private function createOrder(int $cartId): int
    {
        Db::getInstance()->insert('orders', [
            'reference' => $this->reference,
            'id_shop_group' => 1,
            'id_shop' => 1,
            'id_carrier' => 1,
            'id_lang' => 1,
            'id_customer' => (int) $this->customer->id,
            'id_cart' => $cartId,
            'id_currency' => 1,
            'id_address_delivery' => 1,
            'id_address_invoice' => 1,
            'current_state' => 1,
            'secure_key' => md5('guest-tracking-test'),
            'payment' => 'Test payment',
            'conversion_rate' => 1,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
            'delivery_date' => '0000-00-00 00:00:00',
            'invoice_date' => '0000-00-00 00:00:00',
        ]);

        $orderId = (int) Db::getInstance()->Insert_ID();
        $this->orderIds[] = $orderId;

        return $orderId;
    }
}
