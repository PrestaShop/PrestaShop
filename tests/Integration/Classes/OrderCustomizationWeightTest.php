<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Address;
use Cache;
use Cart;
use Combination;
use Configuration;
use Context;
use Customer;
use CustomizationField;
use Db;
use ObjectModel;
use Order;
use OrderDetail;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductType;
use Product;
use ProductAttribute;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;
use Tests\Integration\Utility\ContextMocker;
use Tests\Resources\DatabaseDump;

final class OrderCustomizationWeightTest extends KernelTestCase
{
    private const WEIGHT_DELTA = 0.000001;

    private ?ContextMocker $contextMocker = null;
    private ?KernelInterface $previousKernel = null;

    public static function tearDownAfterClass(): void
    {
        try {
            DatabaseDump::restoreAllTables();
            Configuration::resetStaticCache();
            Product::resetStaticCache();
            Cart::resetStaticCache();
            ObjectModel::resetStaticCache();
            Cache::clean('*');
        } finally {
            parent::tearDownAfterClass();
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        global $kernel;
        $this->previousKernel = $kernel;
        DatabaseDump::restoreAllTables();
        self::bootKernel();
        $kernel = self::$kernel;
        $this->contextMocker = (new ContextMocker())->mockContext();

        Configuration::updateValue('PS_CUSTOMIZATION_FEATURE_ACTIVE', 1);
        Configuration::updateValue('PS_COMBINATION_FEATURE_ACTIVE', 1);
    }

    protected function tearDown(): void
    {
        try {
            if (null !== $this->contextMocker) {
                $this->contextMocker->resetContext();
            }
        } finally {
            global $kernel;
            $kernel = $this->previousKernel;
            parent::tearDown();
        }
    }

    /**
     * @dataProvider getCustomizationWeightCases
     *
     * @see https://github.com/PrestaShop/PrestaShop/issues/43372
     */
    public function testOrderDetailCountsCustomizationWeightOnce(
        bool $withCombination,
        ?float $customizationWeight,
        float $expectedUnitWeight,
    ): void {
        $product = $this->createProduct($withCombination, null !== $customizationWeight);
        $combinationId = $withCombination ? $this->createCombination($product) : 0;
        StockAvailable::setQuantity((int) $product->id, $combinationId, 100);

        $cart = $this->createCart((int) $product->id);
        $customizationId = null === $customizationWeight
            ? 0
            : $this->createCustomization($cart, $product, $combinationId, $customizationWeight);

        self::assertTrue($cart->updateQty(
            3,
            (int) $product->id,
            $combinationId,
            $customizationId
        ));
        Cart::resetStaticCache();

        $products = $cart->getProducts(true);
        self::assertCount(1, $products);
        $weightField = $withCombination ? 'weight_attribute' : 'weight';
        self::assertEqualsWithDelta($expectedUnitWeight, (float) $products[0][$weightField], self::WEIGHT_DELTA);

        $order = $this->createOrder($cart);
        (new OrderDetail())->createList(
            $order,
            $cart,
            (int) Configuration::get('PS_OS_CHEQUE'),
            $products
        );

        $details = OrderDetail::getList((int) $order->id);
        self::assertCount(1, $details);
        $detail = new OrderDetail((int) $details[0]['id_order_detail']);

        self::assertSame((int) $product->id, (int) $detail->product_id);
        self::assertSame($combinationId, (int) $detail->product_attribute_id);
        self::assertSame($customizationId, (int) $detail->id_customization);
        self::assertSame(3, (int) $detail->product_quantity);
        self::assertEqualsWithDelta($expectedUnitWeight, (float) $detail->product_weight, self::WEIGHT_DELTA);
    }

    /**
     * @return iterable<string, array{bool, float|null, float}>
     */
    public static function getCustomizationWeightCases(): iterable
    {
        yield 'simple, positive impact' => [false, 2.0, 3.0];
        yield 'combination, positive impact' => [true, 2.0, 3.5];
        yield 'simple, negative impact' => [false, -0.98, 0.02];
        yield 'simple, no customization' => [false, null, 1.0];
    }

    /**
     * Creates a dedicated physical product with a base weight of 1 kg.
     */
    private function createProduct(bool $withCombination, bool $customizable): Product
    {
        $product = new Product(null, false, (int) Context::getContext()->language->id);
        $product->name = 'Customization weight regression';
        $product->link_rewrite = 'customization-weight-regression';
        $product->product_type = $withCombination ? ProductType::TYPE_COMBINATIONS : ProductType::TYPE_STANDARD;
        $product->active = true;
        $product->available_for_order = true;
        $product->price = 10.0;
        $product->weight = 1.0;
        $product->customizable = (int) $customizable;
        $product->text_fields = (int) $customizable;
        self::assertTrue($product->add());

        return $product;
    }

    /**
     * Adds a non-zero combination weight so omitting its impact also fails the test.
     */
    private function createCombination(Product $product): int
    {
        $combination = new Combination();
        $combination->id_product = (int) $product->id;
        $combination->weight = 0.5;
        $combination->default_on = true;
        self::assertTrue($combination->add());

        $attributes = ProductAttribute::getAttributes((int) Context::getContext()->language->id, true);
        self::assertNotEmpty($attributes);
        self::assertTrue($combination->setAttributes([(int) $attributes[0]['id_attribute']]));
        $product->cache_default_attribute = (int) $combination->id;
        self::assertTrue($product->update());

        return (int) $combination->id;
    }

    /**
     * Creates a customer and an address dedicated to this cart.
     */
    private function createCart(int $productId): Cart
    {
        $context = Context::getContext();
        $customer = new Customer();
        $customer->firstname = 'Weight';
        $customer->lastname = 'Regression';
        $customer->email = 'customization-weight-' . $productId . '@example.com';
        $customer->passwd = password_hash('CustomizationWeight43372', PASSWORD_BCRYPT);
        $customer->id_default_group = (int) Configuration::get('PS_CUSTOMER_GROUP');
        self::assertTrue($customer->add());
        $context->customer = $customer;

        $address = new Address();
        $address->id_customer = (int) $customer->id;
        $address->id_country = (int) $context->country->id;
        $address->firstname = $customer->firstname;
        $address->lastname = $customer->lastname;
        $address->alias = 'Weight regression';
        $address->address1 = '1 Test Street';
        $address->city = 'Test City';
        $address->postcode = '33133';
        self::assertTrue($address->add());

        $cart = new Cart();
        $cart->id_customer = (int) $customer->id;
        $cart->id_address_delivery = (int) $address->id;
        $cart->id_address_invoice = (int) $address->id;
        $cart->id_lang = (int) $context->language->id;
        $cart->id_currency = (int) $context->currency->id;
        $cart->id_shop = (int) $context->shop->id;
        $cart->id_shop_group = (int) $context->shop->id_shop_group;
        $cart->secure_key = $customer->secure_key;
        self::assertTrue($cart->add());
        $context->cart = $cart;

        return $cart;
    }

    /**
     * Creates the order needed by OrderDetail::createList(), without checkout.
     */
    private function createOrder(Cart $cart): Order
    {
        $order = new Order();
        $order->id_cart = (int) $cart->id;
        $order->id_customer = (int) $cart->id_customer;
        $order->secure_key = $cart->secure_key;
        $order->id_address_delivery = (int) $cart->id_address_delivery;
        $order->id_address_invoice = (int) $cart->id_address_invoice;
        $order->id_currency = (int) $cart->id_currency;
        $order->id_lang = (int) $cart->id_lang;
        $order->id_shop = (int) $cart->id_shop;
        $order->id_shop_group = (int) $cart->id_shop_group;
        $order->id_carrier = 0;
        $order->payment = 'Test payment';
        $order->module = 'test';
        $order->total_paid = $order->total_paid_real = 0.0;
        $order->total_products = $order->total_products_wt = 0.0;
        $order->conversion_rate = (float) Context::getContext()->currency->conversion_rate;
        self::assertTrue($order->add());

        return $order;
    }

    /**
     * Simulates a module storing a weight impact on customized_data.
     */
    private function createCustomization(Cart $cart, Product $product, int $combinationId, float $weight): int
    {
        $field = new CustomizationField();
        $field->id_product = (int) $product->id;
        $field->type = Product::CUSTOMIZE_TEXTFIELD;
        $field->required = false;
        $field->name = [(int) $cart->id_lang => 'Weight field'];
        self::assertTrue($field->add());

        $customizationId = $cart->_addCustomization(
            (int) $product->id,
            $combinationId,
            (int) $field->id,
            Product::CUSTOMIZE_TEXTFIELD,
            'Test customization',
            0,
            true
        );
        self::assertGreaterThan(0, $customizationId);
        self::assertTrue(Db::getInstance()->update(
            'customized_data',
            ['weight' => $weight],
            'id_customization = ' . (int) $customizationId
            . ' AND `index` = ' . (int) $field->id
            . ' AND type = ' . Product::CUSTOMIZE_TEXTFIELD
        ));

        return (int) $customizationId;
    }
}
