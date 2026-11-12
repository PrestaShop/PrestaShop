<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Db;
use Module;
use Order;
use PaymentModule;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Shop;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * Order::addWs() does not store the order it is given: it hands the cart to
 * PaymentModule::validateOrder(), which builds the order from that cart. Fields the payload carried are
 * therefore replaced by whatever the cart implies, and only the new id was read back, so the object the
 * webservice rendered its response from still described the request. A GET straight afterwards returned
 * different values for the same order.
 */
class WebserviceOrderCreationTest extends TestCase
{
    use ContextMockerTrait;

    private const STUB_MODULE = 'webserviceordercreationstub';

    private int $createdOrderId = 0;

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        // getInstanceByName() returns whatever sits in Module::$_INSTANCE, which is how the stub takes
        // the place of a real payment module without anything being installed.
        $this->moduleInstances()->setValue(null, [self::STUB_MODULE => new StubPaymentModule()]);
    }

    protected function tearDown(): void
    {
        $this->moduleInstances()->setValue(null, []);

        if ($this->createdOrderId) {
            Db::getInstance()->execute(
                'DELETE FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->createdOrderId
            );
        }

        parent::tearDown();
    }

    public function testTheObjectDescribesTheOrderThatWasStored(): void
    {
        $order = $this->orderAsThePayloadDescribedIt();
        $order->addWs();
        $this->createdOrderId = (int) $order->id;

        $stored = Db::getInstance()->getRow(
            'SELECT id_address_invoice, total_paid_real FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->createdOrderId
        );

        self::assertSame(
            (int) $stored['id_address_invoice'],
            (int) $order->id_address_invoice,
            'the object still carried the invoice address from the payload rather than the stored one'
        );
        self::assertSame(
            (float) $stored['total_paid_real'],
            (float) $order->total_paid_real,
            'the object still carried the amount from the payload rather than the stored one'
        );
    }

    /**
     * The values that were submitted are the ones the cart did not confirm, so they must not survive.
     */
    public function testTheSubmittedValuesDoNotSurvive(): void
    {
        $order = $this->orderAsThePayloadDescribedIt();
        $order->addWs();
        $this->createdOrderId = (int) $order->id;

        self::assertNotSame(5, (int) $order->id_address_invoice);
        self::assertNotSame(999.99, (float) $order->total_paid_real);
    }

    public function testTheNewIdentifierIsStillReturned(): void
    {
        $order = $this->orderAsThePayloadDescribedIt();

        self::assertTrue($order->addWs());
        $this->createdOrderId = (int) $order->id;

        self::assertGreaterThan(0, $this->createdOrderId);
    }

    private function orderAsThePayloadDescribedIt(): Order
    {
        $order = new Order();
        $order->module = self::STUB_MODULE;
        $order->id_cart = 1;
        $order->id_customer = 1;
        // What a client asked for, and what validateOrder() will not honour.
        $order->id_address_invoice = 5;
        $order->total_paid_real = 999.99;

        return $order;
    }

    private function moduleInstances(): ReflectionProperty
    {
        $property = (new ReflectionClass(Module::class))->getProperty('_INSTANCE');
        $property->setAccessible(true);

        return $property;
    }
}

/**
 * Stands in for a payment module. validateOrder() writes the order the way the real one does, from the
 * cart rather than from the object, so the values it stores deliberately differ from the submitted ones.
 */
class StubPaymentModule extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'webserviceordercreationstub';
        $this->displayName = 'Webservice order creation stub';
    }

    public function validateOrder(
        $id_cart,
        $id_order_state,
        $amount_paid,
        $payment_method = 'Unknown',
        $message = null,
        $extra_vars = [],
        $currency_special = null,
        $dont_touch_amount = false,
        $secure_key = false,
        ?Shop $shop = null,
        ?string $order_reference = null
    ) {
        Db::getInstance()->insert('orders', [
            'reference' => 'WSSTUB' . substr((string) time(), -4),
            'id_shop_group' => 1,
            'id_shop' => 1,
            'id_carrier' => 1,
            'id_lang' => 1,
            'id_customer' => 1,
            'id_cart' => (int) $id_cart,
            'id_currency' => 1,
            'id_address_delivery' => 2,
            // The cart says 2; the payload asked for 5.
            'id_address_invoice' => 2,
            'current_state' => 1,
            'secure_key' => md5('webservice-order-creation-stub'),
            'payment' => 'Stub',
            'module' => 'webserviceordercreationstub',
            'conversion_rate' => 1,
            // The cart says nothing has been paid; the payload claimed 999.99.
            'total_paid_real' => 0,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
            'delivery_date' => '0000-00-00 00:00:00',
            'invoice_date' => '0000-00-00 00:00:00',
        ]);

        $this->currentOrder = (int) Db::getInstance()->Insert_ID();

        return true;
    }
}
