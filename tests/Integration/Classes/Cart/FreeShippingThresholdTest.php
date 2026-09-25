<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Cart;

use Address;
use Carrier;
use Cart;
use CartRule;
use Configuration;
use Context;
use Currency;
use DateTime;
use Db;
use Group;
use PrestaShop\PrestaShop\Core\Domain\Carrier\ValueObject\OutOfRangeBehavior;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Shipping preferences carry the sentence "Coupons are not taken into account when calculating free
 * shipping" next to the free shipping threshold, and Cart::getPackageShippingCostValue() repeats it in a
 * comment. Until 1.7.5.2 the code agreed: Cart::BOTH_WITHOUT_SHIPPING fell through to ONLY_PRODUCTS and
 * returned the row total. From 1.7.6.0 it applies cart rules, so a voucher started pushing carts back below
 * the threshold.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/38274
 */
class FreeShippingThresholdTest extends KernelTestCase
{
    /** @var int */
    private static $idAddress;

    /** @var float */
    private const FREE_SHIPPING_FROM = 50.0;

    /** @var float what the carrier charges when free shipping does NOT apply */
    private const SHIPPING_COST = 7.0;

    /** @var int|null */
    private static $idCarrier;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        Configuration::loadConfiguration();
        Context::getContext()->currency = Currency::getDefaultCurrency();
        Group::clearCachedValues();

        Configuration::updateValue('PS_TAX_ADDRESS_TYPE', 'id_address_invoice');
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);
        Configuration::set('PS_CART_RULE_FEATURE_ACTIVE', true);
        Configuration::set('PS_ATCP_SHIPWRAP', false);
        Configuration::updateValue('PS_SHIPPING_FREE_PRICE', self::FREE_SHIPPING_FROM);
        Configuration::updateValue('PS_SHIPPING_FREE_WEIGHT', 0);

        // Pre-existing cart rules would change the totals under the test
        Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'cart_rule SET active = 0');

        if (null === self::$idAddress) {
            self::$idAddress = $this->makeAddress()->id;
        }
    }

    protected function tearDown(): void
    {
        Configuration::updateValue('PS_SHIPPING_FREE_PRICE', 0);
        parent::tearDown();
    }

    /**
     * Control. A cart BELOW the threshold pays the carrier. Without this the other two assertions are
     * vacuous: getPackageShippingCostValue() also returns 0 when no carrier can be resolved, so "0" alone
     * does not prove that free shipping was applied.
     */
    public function testACartBelowTheThresholdPaysTheCarrier(): void
    {
        $cart = $this->makeCartWithProduct(self::FREE_SHIPPING_FROM - 10);

        $this->assertEquals(
            self::SHIPPING_COST,
            (float) $cart->getPackageShippingCost(self::idCarrier(), false),
            'the carrier must charge, or a 0 below means "no carrier" rather than "free shipping"'
        );
    }

    /**
     * Control. Above the threshold, the same carrier ships free.
     */
    public function testACartAboveTheThresholdShipsFree(): void
    {
        $cart = $this->makeCartWithProduct(self::FREE_SHIPPING_FROM + 50);

        $this->assertEquals(0.0, (float) $cart->getPackageShippingCost(self::idCarrier(), false));
    }

    /**
     * The reported case: a voucher drags the discounted total under the threshold. Free shipping must not
     * be withdrawn, because the setting is documented as ignoring coupons.
     */
    public function testAVoucherDoesNotWithdrawFreeShipping(): void
    {
        $price = self::FREE_SHIPPING_FROM + 50;
        $cart = $this->makeCartWithProduct($price);

        $cartRule = $this->makeCartRule($price - 1);
        $cart->addCartRule((int) $cartRule->id);
        $cart = new Cart((int) $cart->id);
        Context::getContext()->cart = $cart;

        // the voucher really does take the discounted total below the threshold
        $this->assertLessThan(
            self::FREE_SHIPPING_FROM,
            $cart->getOrderTotal(true, Cart::BOTH_WITHOUT_SHIPPING),
            'the voucher must take the discounted total below the threshold, or nothing is being tested'
        );
        // while the products total, which the threshold is documented to use, stays above it
        $this->assertGreaterThanOrEqual(
            self::FREE_SHIPPING_FROM,
            $cart->getOrderTotal(true, Cart::ONLY_PRODUCTS)
        );

        $this->assertEquals(0.0, (float) $cart->getPackageShippingCost(self::idCarrier(), false));
    }

    private static function idCarrier(): int
    {
        if (null !== self::$idCarrier) {
            return self::$idCarrier;
        }

        $carrier = new Carrier(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $carrier->name = 'free shipping threshold carrier';
        $carrier->delay = 'whenever';
        $carrier->range_behavior = (bool) OutOfRangeBehavior::USE_HIGHEST_RANGE;
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_PRICE;
        $carrier->shipping_handling = false;
        self::assertTrue($carrier->save());

        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'range_price (id_carrier, delimiter1, delimiter2)
             VALUES (' . (int) $carrier->id . ', 0, 100000)'
        );
        $idRangePrice = (int) Db::getInstance()->Insert_ID();
        self::assertGreaterThan(0, $idRangePrice);

        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'delivery (id_carrier, id_range_price, id_range_weight, id_zone, price)
             SELECT ' . (int) $carrier->id . ', ' . $idRangePrice . ', 0, id_zone, ' . self::SHIPPING_COST . '
             FROM ' . _DB_PREFIX_ . 'zone'
        );
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'carrier_zone (id_carrier, id_zone)
             SELECT ' . (int) $carrier->id . ', id_zone FROM ' . _DB_PREFIX_ . 'zone'
        );

        return self::$idCarrier = (int) $carrier->id;
    }

    private function makeCartWithProduct(float $price): Cart
    {
        $product = new Product();
        $product->name = ['1' => 'Free shipping threshold product'];
        $product->link_rewrite = ['1' => 'free-shipping-threshold-product'];
        $product->price = $price;
        $product->id_tax_rules_group = 0;
        $product->quantity = 100;
        $product->add();

        $cart = new Cart(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $cart->id_currency = Currency::getDefaultCurrencyId();
        $cart->id_address_invoice = self::$idAddress;
        $cart->id_address_delivery = self::$idAddress;
        $this->assertTrue($cart->save());
        Context::getContext()->cart = $cart;

        $cart->updateQty(1, (int) $product->id);

        $cart = new Cart((int) $cart->id);
        Context::getContext()->cart = $cart;

        return $cart;
    }

    private function makeCartRule(float $amount): CartRule
    {
        $from = new DateTime();
        $to = new DateTime();
        $from->modify('-2 days');
        $to->modify('+2 days');

        $cartRule = new CartRule(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $cartRule->name = 'Free shipping threshold voucher';
        $cartRule->date_from = $from->format('Y-m-d H:i:s');
        $cartRule->date_to = $to->format('Y-m-d H:i:s');
        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = 1;
        $cartRule->reduction_amount = $amount;
        $cartRule->reduction_tax = true;
        $cartRule->reduction_currency = Currency::getDefaultCurrencyId();
        $cartRule->active = true;
        $this->assertTrue($cartRule->add());

        return $cartRule;
    }

    private function makeAddress(): Address
    {
        $address = new Address();
        $address->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $address->id_state = 0;
        $address->firstname = 'Free';
        $address->lastname = 'Shipping';
        $address->address1 = '1 threshold street';
        $address->city = 'Paris';
        $address->postcode = '75001';
        $address->alias = 'free-shipping-threshold';
        $address->add();

        return $address;
    }
}
