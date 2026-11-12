<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Order;

use Cache;
use Cart;
use Configuration;
use Context;
use Db;
use Module;
use Order;
use PaymentModule;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Shop;
use StockAvailable;
use Tests\Integration\Utility\ContextMockerTrait;
use WebserviceRequest;

/**
 * Whether a shop accepts an order for stock it does not have is a merchant setting, and
 * Cart::checkQuantities() is where that setting is applied - but the front office cart controller is
 * its only caller. An order created through the webservice was therefore accepted whatever the stock
 * was, and drove the available quantity negative on the very shops that had asked for out-of-stock
 * ordering to be refused.
 */
class WebserviceOrderStockTest extends TestCase
{
    use ContextMockerTrait;

    private const STUB_MODULE = 'webserviceorderstockstub';
    private const IN_STOCK = 5;
    private const REQUESTED = 50;

    private int $productId = 0;
    private int $cartId = 0;
    private int $createdOrderId = 0;
    private array $stockBefore = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        // getInstanceByName() hands back whatever sits in Module::$_INSTANCE, which is how the stub
        // takes the place of a real payment module without anything being installed.
        $this->moduleInstances()->setValue(null, [self::STUB_MODULE => new StockGuardStubPaymentModule()]);
        WebserviceRequest::resetStaticCache();

        $this->productId = $this->aSimpleProductOnSale();
        // The product is shared with the rest of the suite, so what this test does to its stock has to
        // be undone. Restoring only the rows the test created would still leave it at a quantity no
        // other test expects.
        $this->stockBefore = Db::getInstance()->getRow(
            'SELECT quantity, physical_quantity, reserved_quantity, out_of_stock
             FROM ' . _DB_PREFIX_ . 'stock_available
             WHERE id_product = ' . $this->productId . ' AND id_product_attribute = 0'
        ) ?: [];
        StockAvailable::setQuantity($this->productId, 0, self::IN_STOCK);
        // 0 is "deny", the setting whose whole purpose is to refuse this order.
        StockAvailable::setProductOutOfStock($this->productId, 0);

        $this->cartId = $this->aCartAskingFor(self::REQUESTED);
    }

    protected function tearDown(): void
    {
        $this->moduleInstances()->setValue(null, []);
        WebserviceRequest::resetStaticCache();

        if ($this->createdOrderId) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'orders WHERE id_order = ' . $this->createdOrderId);
        }
        if ($this->cartId) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart_product WHERE id_cart = ' . $this->cartId);
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart WHERE id_cart = ' . $this->cartId);
        }
        if ($this->stockBefore) {
            Db::getInstance()->execute(
                'UPDATE ' . _DB_PREFIX_ . 'stock_available SET'
                . ' quantity = ' . (int) $this->stockBefore['quantity']
                . ', physical_quantity = ' . (int) $this->stockBefore['physical_quantity']
                . ', reserved_quantity = ' . (int) $this->stockBefore['reserved_quantity']
                . ', out_of_stock = ' . (int) $this->stockBefore['out_of_stock']
                . ' WHERE id_product = ' . $this->productId . ' AND id_product_attribute = 0'
            );
            Cache::clean('*');
        }

        parent::tearDown();
    }

    public function testTheOrderIsRefusedWhenTheShopRefusesOutOfStockOrdering(): void
    {
        $order = $this->orderForTheCart();

        self::assertFalse($order->addWs());
        self::assertSame(0, (int) $order->id, 'no order may be created for stock the shop does not have');
    }

    public function testTheRefusalNamesTheProductAndBothQuantities(): void
    {
        $this->orderForTheCart()->addWs();

        $errors = WebserviceRequest::getInstance()->errors;
        self::assertNotEmpty($errors, 'the refusal has to reach the client as a webservice error');

        [, $message] = $errors[0];
        self::assertStringContainsString((string) $this->productId, $message);
        self::assertStringContainsString((string) self::REQUESTED, $message, 'the client has to be told what it asked for');
        self::assertStringContainsString((string) self::IN_STOCK, $message, 'and how much of it there is');
    }

    public function testTheStockIsLeftAloneWhenTheOrderIsRefused(): void
    {
        $this->orderForTheCart()->addWs();

        self::assertSame(
            self::IN_STOCK,
            (int) StockAvailable::getQuantityAvailableByProduct($this->productId),
            'a refused order must not move the stock it was refused for'
        );
    }

    /**
     * The setting reads both ways. A shop that sells on backorder relies on this call succeeding, so
     * the guard must never reach a product the merchant has allowed to go out of stock.
     */
    public function testAShopThatAllowsOutOfStockOrderingStillCreatesTheOrder(): void
    {
        StockAvailable::setProductOutOfStock($this->productId, 1);

        $order = $this->orderForTheCart();

        self::assertTrue($order->addWs());
        $this->createdOrderId = (int) $order->id;
        self::assertGreaterThan(0, $this->createdOrderId);
    }

    public function testAnOrderThatFitsInTheStockIsStillCreated(): void
    {
        Db::getInstance()->execute(
            'UPDATE ' . _DB_PREFIX_ . 'cart_product SET quantity = 1 WHERE id_cart = ' . $this->cartId
        );
        Cart::resetStaticCache();

        $order = $this->orderForTheCart();

        self::assertTrue($order->addWs());
        $this->createdOrderId = (int) $order->id;
        self::assertGreaterThan(0, $this->createdOrderId);
    }

    private function orderForTheCart(): Order
    {
        $order = new Order();
        $order->module = self::STUB_MODULE;
        $order->id_cart = $this->cartId;
        $order->id_customer = (int) (new Cart($this->cartId))->id_customer;

        return $order;
    }

    /**
     * A product with no combination, so the cart row addresses the stock the test sets directly.
     */
    private function aSimpleProductOnSale(): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT p.id_product
             FROM ' . _DB_PREFIX_ . 'product p
             INNER JOIN ' . _DB_PREFIX_ . 'product_shop ps ON ps.id_product = p.id_product
             WHERE ps.active = 1
               AND ps.available_for_order = 1
               AND p.id_product NOT IN (SELECT id_product FROM ' . _DB_PREFIX_ . 'product_attribute)
             ORDER BY p.id_product'
        );
    }

    private function aCartAskingFor(int $quantity): int
    {
        $cart = new Cart();
        $cart->id_customer = (int) Db::getInstance()->getValue(
            'SELECT id_customer FROM ' . _DB_PREFIX_ . 'customer WHERE deleted = 0 AND active = 1'
        );
        $address = (int) Db::getInstance()->getValue(
            'SELECT id_address FROM ' . _DB_PREFIX_ . 'address WHERE id_customer = ' . $cart->id_customer . ' AND deleted = 0'
        );
        $cart->id_address_delivery = $address;
        $cart->id_address_invoice = $address;
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $cart->id_shop = (int) Configuration::get('PS_SHOP_DEFAULT');
        $cart->add();

        Context::getContext()->cart = $cart;
        // The webservice fills a cart through this setter, which inserts the rows as given. updateQty()
        // would refuse the quantity outright (Cart::updateQty), so it could never set up what is being
        // tested here, and it is not the call the webservice makes anyway.
        $cart->setWsCartRows([
            ['id_product' => $this->productId, 'id_product_attribute' => 0, 'quantity' => $quantity],
        ]);

        return (int) $cart->id;
    }

    private function moduleInstances(): ReflectionProperty
    {
        $property = (new ReflectionClass(Module::class))->getProperty('_INSTANCE');
        $property->setAccessible(true);

        return $property;
    }
}

/**
 * Stands in for a payment module so the call that follows the guard can be reached without one being
 * installed. It records an order the way validateOrder() would, from the cart it is handed.
 */
class StockGuardStubPaymentModule extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'webserviceorderstockstub';
        $this->displayName = 'Webservice order stock stub';
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
        $cart = new Cart((int) $id_cart);

        Db::getInstance()->insert('orders', [
            'reference' => 'WSSTK' . substr((string) time(), -5),
            'id_shop_group' => 1,
            'id_shop' => (int) $cart->id_shop,
            'id_carrier' => 1,
            'id_lang' => (int) $cart->id_lang,
            'id_customer' => (int) $cart->id_customer,
            'id_cart' => (int) $id_cart,
            'id_currency' => (int) $cart->id_currency,
            'id_address_delivery' => (int) $cart->id_address_delivery,
            'id_address_invoice' => (int) $cart->id_address_invoice,
            'current_state' => (int) $id_order_state,
            'secure_key' => md5('webservice-order-stock-stub'),
            'payment' => 'Stub',
            'module' => 'webserviceorderstockstub',
            'conversion_rate' => 1,
            'total_paid_real' => 0,
            'date_add' => date('Y-m-d H:i:s'),
            'date_upd' => date('Y-m-d H:i:s'),
            'delivery_date' => '0000-00-00 00:00:00',
            'invoice_date' => '0000-00-00 00:00:00',
        ]);

        $this->currentOrder = (int) Db::getInstance()->Insert_ID();

        // The real validateOrder() takes the ordered units out of the stock through OrderDetail. Doing
        // the same here is what makes "the stock was left alone" mean something: without it that
        // assertion would hold just as well when the guard never ran at all.
        foreach ($cart->getProducts() as $product) {
            StockAvailable::updateQuantity(
                (int) $product['id_product'],
                (int) $product['id_product_attribute'],
                -(int) $product['cart_quantity'],
                (int) $cart->id_shop
            );
        }

        return true;
    }
}
