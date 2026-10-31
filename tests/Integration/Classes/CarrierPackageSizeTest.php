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
 * A carrier declares its maximum package width, height and depth in whole centimetres, while a product
 * carries decimal dimensions. Both sides were cast to int before being compared, so a 2.5 cm product was
 * measured as 2 and a carrier limited to 2 cm accepted it.
 */
class CarrierPackageSizeTest extends KernelTestCase
{
    private const CARRIER_MAX_SIZE = 2;
    private const TOO_WIDE = 2.5;
    private const FITS_EXACTLY = 2.0;
    private const FITS = 1.5;

    /** @var int */
    private static $idAddress;
    /** @var int */
    private static $idCarrier;
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
        $context->container = self::getContainer();
        $context->currency = new Currency(Currency::getDefaultCurrencyId());
        $context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $context->customer = new Customer();
        $context->customer->id_default_group = (int) Configuration::get('PS_UNIDENTIFIED_GROUP');

        self::removeFixture();
        // Carriers and their availability are memoised per request, so a later test would otherwise be
        // answered with the previous test's deleted carrier.
        Cache::clean('*');
        Carrier::resetStaticCache();
        Cart::resetStaticCache();
        Product::resetStaticCache();

        self::$idCarrier = (int) self::makeCarrierLimitedTo(self::CARRIER_MAX_SIZE)->id;
    }

    /**
     * The report: a product half a centimetre over the limit is offered the carrier anyway.
     */
    public function testAProductWiderThanTheCarrierIsRefused(): void
    {
        [$carriers, $errors] = self::availableCarriersFor(self::TOO_WIDE);

        $this->assertNotContains(
            self::$idCarrier,
            $carriers,
            sprintf('a %s cm product does not fit a carrier limited to %d cm', self::TOO_WIDE, self::CARRIER_MAX_SIZE)
        );
        $this->assertSame(Carrier::SHIPPING_SIZE_EXCEPTION, $errors[self::$idCarrier] ?? null);
    }

    /**
     * The same on the two other axes, because all three were cast the same way.
     */
    public function testAProductTallerOrDeeperThanTheCarrierIsRefused(): void
    {
        foreach (['height', 'depth'] as $axis) {
            [$carriers] = self::availableCarriersFor(self::TOO_WIDE, $axis);

            $this->assertNotContains(self::$idCarrier, $carriers, sprintf('%s is checked too', $axis));
        }
    }

    /**
     * The boundary the fix must not move: a product exactly the size of the limit still fits.
     */
    public function testAProductExactlyTheSizeOfTheLimitIsAccepted(): void
    {
        [$carriers] = self::availableCarriersFor(self::FITS_EXACTLY);

        $this->assertContains(self::$idCarrier, $carriers);
    }

    public function testAProductSmallerThanTheLimitIsAccepted(): void
    {
        [$carriers] = self::availableCarriersFor(self::FITS);

        $this->assertContains(self::$idCarrier, $carriers);
    }

    /**
     * A carrier that declares no limit takes anything, which is what a 0 in those columns means.
     */
    public function testACarrierWithNoDeclaredLimitTakesAnySize(): void
    {
        $unlimited = (int) self::makeCarrierLimitedTo(0)->id;

        [$carriers] = self::availableCarriersFor(self::TOO_WIDE);

        $this->assertContains($unlimited, $carriers);
    }

    /**
     * @return array{0: array<int>, 1: array<int, int>}
     */
    private static function availableCarriersFor(float $size, string $axis = 'width'): array
    {
        $product = self::makeProduct('CarrierPackageSizeProduct-36319', $axis, $size);

        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart = new Cart(null, $idLang);
        $cart->id_currency = Currency::getDefaultCurrencyId();
        $cart->id_address_invoice = self::$idAddress;
        $cart->id_address_delivery = self::$idAddress;
        $cart->save();
        self::$cartIds[] = (int) $cart->id;

        Context::getContext()->cart = $cart;
        $cart->updateQty(1, (int) $product->id);
        $cart = new Cart((int) $cart->id, $idLang);
        Context::getContext()->cart = $cart;

        Cache::clean('*');

        $errors = [];
        $carriers = Carrier::getAvailableCarrierList(
            $product,
            0,
            self::$idAddress,
            (int) Context::getContext()->shop->id,
            $cart,
            $errors
        );

        return [array_map('intval', array_values($carriers)), $errors];
    }

    private static function makeCarrierLimitedTo(int $maxSize): Carrier
    {
        $carrier = new Carrier(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $carrier->name = 'PackageSizeCarrier-36319-' . $maxSize . '-' . uniqid();
        $carrier->delay = 'test';
        $carrier->active = true;
        $carrier->need_range = true;
        $carrier->shipping_method = Carrier::SHIPPING_METHOD_WEIGHT;
        $carrier->range_behavior = (bool) OutOfRangeBehavior::DISABLED;
        $carrier->shipping_handling = false;
        // Left at 0 so only the size check decides.
        $carrier->max_weight = 0;
        $carrier->max_width = $maxSize;
        $carrier->max_height = $maxSize;
        $carrier->max_depth = $maxSize;
        $carrier->save();

        $carrier->id_reference = (int) $carrier->id;
        $carrier->save();

        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'range_weight (id_carrier, delimiter1, delimiter2)
             VALUES (' . (int) $carrier->id . ', 0, 1000)'
        );
        $idRangeWeight = (int) Db::getInstance()->Insert_ID();

        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'delivery (id_carrier, id_shop, id_shop_group, id_range_price, id_range_weight, id_zone, price)
             SELECT ' . (int) $carrier->id . ', NULL, NULL, 0, ' . $idRangeWeight . ', id_zone, 10 FROM ' . _DB_PREFIX_ . 'zone'
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

    private static function makeProduct(string $name, string $axis, float $size): Product
    {
        $product = new Product(null, false, (int) Configuration::get('PS_LANG_DEFAULT'));
        $product->name = $name;
        $product->price = 10;
        $product->weight = 1;
        $product->width = 0;
        $product->height = 0;
        $product->depth = 0;
        $product->{$axis} = $size;
        $product->active = true;
        $product->visibility = 'both';
        $product->link_rewrite = Tools::str2url($name . uniqid());
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

        foreach ($db->executeS('SELECT id_carrier FROM ' . _DB_PREFIX_ . 'carrier WHERE name LIKE "PackageSizeCarrier-36319-%"') as $row) {
            $idCarrier = (int) $row['id_carrier'];
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'carrier_zone WHERE id_carrier = ' . $idCarrier);
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'carrier_group WHERE id_carrier = ' . $idCarrier);
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'delivery WHERE id_carrier = ' . $idCarrier);
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'range_weight WHERE id_carrier = ' . $idCarrier);
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'carrier_lang WHERE id_carrier = ' . $idCarrier);
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'carrier_shop WHERE id_carrier = ' . $idCarrier);
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'carrier WHERE id_carrier = ' . $idCarrier);
        }

        foreach ($db->executeS('SELECT id_product FROM ' . _DB_PREFIX_ . 'product WHERE reference = "" AND id_product IN (SELECT id_product FROM ' . _DB_PREFIX_ . 'product_lang WHERE name LIKE "CarrierPackageSizeProduct-36319%")') as $row) {
            $product = new Product((int) $row['id_product']);
            $product->delete();
        }
    }
}
