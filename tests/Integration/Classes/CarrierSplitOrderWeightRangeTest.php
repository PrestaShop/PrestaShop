<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Address;
use Cache;
use Carrier;
use Cart;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Db;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tools;

/**
 * When a cart needs two carriers because its products are linked to different ones, each carrier only
 * ever ships its own package. The weight range of a carrier was nevertheless checked against the weight
 * of the whole cart, so two 2 kg products shipped by two carriers each limited to 3 kg disabled both,
 * and the customer was offered no delivery option at all.
 */
class CarrierSplitOrderWeightRangeTest extends KernelTestCase
{
    private const RANGE_MAX_WEIGHT = 3.0;
    private const PRODUCT_WEIGHT = 2.0;
    private const SHIPPING_PRICE = 10.0;

    /** @var int */
    private static $idAddress;
    /** @var array<int> */
    private static $idCarriers = [];
    /** @var array<Product> */
    private static $products = [];
    /** @var Cart */
    private static $cart;
    /** @var array<int> */
    private static $cartIds = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        Configuration::loadConfiguration();
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);

        self::$idAddress = (int) self::makeAddress()->id;
    }

    public static function tearDownAfterClass(): void
    {
        self::removeFixture();
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        self::bootKernel();

        $context = Context::getContext();
        // Cart::getOrderTotal() builds a calculator out of the container, and price computation
        // reads the rounding precision off the context currency.
        $context->container = self::getContainer();
        $context->currency = new Currency(Currency::getDefaultCurrencyId());
        $context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $context->customer = new Customer();
        $context->customer->id_default_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');

        self::removeFixture();
        // Carriers, packages and delivery options are all memoised per request; without this a later
        // test is answered with the previous test's (deleted) carriers.
        Cache::clean('*');
        Carrier::resetStaticCache();
        Cart::resetStaticCache();
        Product::resetStaticCache();

        self::buildFixture();
    }

    /**
     * The premise of the report: neither carrier can take the whole cart, but each can take its own half.
     */
    public function testNeitherCarrierCanTakeTheWholeCartButEachCanTakeItsOwnPackage(): void
    {
        self::assertSame(2.0 * self::PRODUCT_WEIGHT, (float) self::$cart->getTotalWeight());
        self::assertGreaterThan(self::RANGE_MAX_WEIGHT, (float) self::$cart->getTotalWeight());
        self::assertLessThanOrEqual(self::RANGE_MAX_WEIGHT, self::PRODUCT_WEIGHT);
    }

    /**
     * Each package holds one product, so its weight is half the cart. Asserted explicitly because a
     * product list missing the weight keys would silently weigh 0 and let every carrier pass.
     */
    public function testEachPackageWeighsOnlyItsOwnProducts(): void
    {
        $packages = self::$cart->getPackageList(true);

        $weights = [];
        foreach ($packages as $packageList) {
            foreach ($packageList as $package) {
                $weights[] = (float) self::$cart->getTotalWeight($package['product_list']);
            }
        }

        self::assertCount(2, $weights, 'the cart splits into two packages');
        foreach ($weights as $weight) {
            self::assertSame(self::PRODUCT_WEIGHT, $weight, 'a package weighs its own products, not the cart');
        }
    }

    /**
     * The reported symptom.
     */
    public function testDeliveryOptionsAreOfferedWhenEachPackageFitsItsCarrier(): void
    {
        self::assertNotEmpty(
            self::$cart->getDeliveryOptionList(null, true),
            'each package is within its carrier range, so the checkout must offer a delivery option'
        );
    }

    /**
     * Both carriers must survive; dropping either is what empties the option list.
     */
    public function testBothCarriersRemainAvailableForTheirOwnProduct(): void
    {
        $packages = self::$cart->getPackageList(true);

        $offered = [];
        foreach ($packages as $packageList) {
            foreach ($packageList as $package) {
                foreach ($package['carrier_list'] as $idCarrier) {
                    $offered[] = (int) $idCarrier;
                }
            }
        }

        foreach (self::$idCarriers as $idCarrier) {
            self::assertContains($idCarrier, $offered, 'carrier ' . $idCarrier . ' ships a package within its range');
        }
    }

    /**
     * getDeliveryOptionList() prices each package by handing its own product list to
     * getPackageShippingCost(). The weight range has to be checked against that list too, otherwise the
     * cost comes back false for a package that is comfortably inside the carrier's range.
     */
    public function testAPackageIsPricedAgainstItsOwnWeight(): void
    {
        $country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));

        $priced = 0;
        foreach (self::$cart->getPackageList(true) as $packageList) {
            foreach ($packageList as $package) {
                foreach ($package['carrier_list'] as $idCarrier) {
                    if (!in_array((int) $idCarrier, self::$idCarriers, true)) {
                        continue;
                    }

                    $cost = self::$cart->getPackageShippingCost((int) $idCarrier, true, $country, $package['product_list']);

                    self::assertNotFalse($cost, 'the package is within the range of the carrier shipping it');
                    self::assertGreaterThan(0, (float) $cost, 'a priced range must yield a cost');
                    ++$priced;
                }
            }
        }

        self::assertSame(2, $priced, 'both packages are priced by their own carrier');
    }

    /**
     * The ordinary cart, shipped whole by one carrier, must be unaffected: with no product list the
     * checks fall back to the cart weight, which is what they always used.
     */
    public function testASingleCarrierCartIsUnaffected(): void
    {
        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $cart = new Cart(null, $idLang);
        $cart->id_currency = Currency::getDefaultCurrencyId();
        $cart->id_address_invoice = self::$idAddress;
        $cart->id_address_delivery = self::$idAddress;
        $cart->save();
        self::$cartIds[] = (int) $cart->id;

        Context::getContext()->cart = $cart;
        $cart->updateQty(1, (int) self::$products[0]->id);
        $cart = new Cart((int) $cart->id, $idLang);
        Context::getContext()->cart = $cart;

        self::assertSame(self::PRODUCT_WEIGHT, (float) $cart->getTotalWeight(), 'one product, within range');

        $country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        self::assertSame(
            self::SHIPPING_PRICE,
            (float) $cart->getPackageShippingCost(self::$idCarriers[0], true, $country, null),
            'with no product list the whole cart is weighed, as before'
        );
        self::assertNotEmpty($cart->getDeliveryOptionList(null, true));

        Context::getContext()->cart = self::$cart;
    }

    private static function buildFixture(): void
    {
        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');

        self::$idCarriers = [];
        self::$products = [];

        for ($i = 0; $i < 2; ++$i) {
            $carrier = self::makeWeightCarrier('SplitOrderCarrier' . $i . '-35573');
            $product = self::makeProduct('SplitOrderProduct' . $i . '-35573');

            // This product can only be shipped by this carrier, which is what forces the split.
            Db::getInstance()->insert('product_carrier', [
                'id_product' => (int) $product->id,
                'id_carrier_reference' => (int) $carrier->id_reference,
                'id_shop' => (int) Context::getContext()->shop->id,
            ]);

            self::$idCarriers[] = (int) $carrier->id;
            self::$products[] = $product;
        }

        $cart = new Cart(null, $idLang);
        $cart->id_currency = Currency::getDefaultCurrencyId();
        $cart->id_address_invoice = self::$idAddress;
        $cart->id_address_delivery = self::$idAddress;
        $cart->save();
        Context::getContext()->cart = $cart;

        foreach (self::$products as $product) {
            $cart->updateQty(1, (int) $product->id);
        }

        self::$cartIds[] = (int) $cart->id;
        self::$cart = new Cart((int) $cart->id, $idLang);
        Context::getContext()->cart = self::$cart;
    }

    private static function makeWeightCarrier(string $name): Carrier
    {
        $carrier = new Carrier(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $carrier->name = $name;
        $carrier->delay = 'test';
        $carrier->active = true;
        $carrier->need_range = true;
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_WEIGHT;
        $carrier->range_behavior = (bool) OutOfRangeBehavior::DISABLED;
        $carrier->shipping_handling = false;
        // Left at 0 so the separate max_weight check does not interfere with what is measured here.
        $carrier->max_weight = 0;
        $carrier->save();

        $carrier->id_reference = (int) $carrier->id;
        $carrier->save();

        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'range_weight (id_carrier, delimiter1, delimiter2)
             VALUES (' . (int) $carrier->id . ', 0, ' . (float) self::RANGE_MAX_WEIGHT . ')'
        );
        $idRangeWeight = (int) Db::getInstance()->Insert_ID();

        // id_shop / id_shop_group stay NULL so the range applies to every shop, which is what
        // Carrier::sqlDeliveryRangeShop() looks for.
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'delivery (id_carrier, id_shop, id_shop_group, id_range_price, id_range_weight, id_zone, price)
             SELECT ' . (int) $carrier->id . ', NULL, NULL, 0, ' . $idRangeWeight . ', id_zone, ' . self::SHIPPING_PRICE . ' FROM ' . _DB_PREFIX_ . 'zone'
        );
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier_zone (id_carrier, id_zone)
             SELECT ' . (int) $carrier->id . ', id_zone FROM ' . _DB_PREFIX_ . 'zone'
        );
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier_group (id_carrier, id_group)
             SELECT ' . (int) $carrier->id . ', id_group FROM ' . _DB_PREFIX_ . 'group'
        );

        return $carrier;
    }

    private static function makeProduct(string $name): Product
    {
        $product = new Product(null, false, (int) Configuration::get('PS_LANG_DEFAULT'));
        $product->name = $name;
        $product->price = 10;
        $product->weight = self::PRODUCT_WEIGHT;
        $product->active = true;
        $product->visibility = 'both';
        $product->link_rewrite = Tools::str2url($name);
        $product->save();

        return $product;
    }

    private static function makeAddress(): Address
    {
        $address = new Address();
        $address->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $address->firstname = 'Unit';
        $address->lastname = 'Tester';
        $address->address1 = '55 rue Raspail';
        $address->alias = microtime() . getmypid();
        $address->city = 'Levallois';
        $address->save();

        return $address;
    }

    private static function removeFixture(): void
    {
        $db = Db::getInstance();

        foreach (self::$cartIds as $idCart) {
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart_product WHERE id_cart = ' . (int) $idCart);
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart WHERE id_cart = ' . (int) $idCart);
        }
        self::$cartIds = [];

        foreach ($db->executeS('SELECT id_carrier FROM ' . _DB_PREFIX_ . 'carrier WHERE name LIKE "SplitOrderCarrier%-35573"') as $row) {
            $id = (int) $row['id_carrier'];
            foreach (['delivery', 'range_weight', 'carrier_zone', 'carrier_group', 'carrier_lang', 'carrier_shop', 'carrier'] as $table) {
                $db->execute('DELETE FROM ' . _DB_PREFIX_ . $table . ' WHERE id_carrier = ' . $id);
            }
        }

        $rows = $db->executeS(
            'SELECT DISTINCT p.id_product FROM ' . _DB_PREFIX_ . 'product p
             JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product
             WHERE pl.name LIKE "SplitOrderProduct%-35573"'
        );
        foreach ($rows as $row) {
            $id = (int) $row['id_product'];
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'product_carrier WHERE id_product = ' . $id);
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart_product WHERE id_product = ' . $id);
            (new Product($id))->delete();
        }
    }
}
