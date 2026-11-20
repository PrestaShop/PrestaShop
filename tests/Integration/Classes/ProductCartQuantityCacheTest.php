<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cache;
use Cart;
use CartRule;
use Configuration;
use Context;
use Currency;
use Db;
use DbPDO;
use Language;
use Product;
use Shop;
use SpecificPrice;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class ProductCartQuantityCacheTest extends KernelTestCase
{
    private int $cartQuantityQueries = 0;

    /**
     * @var array<string, mixed>
     */
    private array $contextBackup = [];

    protected function setUp(): void
    {
        parent::setUp();

        $realDb = Db::getInstance();
        $spy = $this->getMockBuilder(DbPDO::class)
            ->setConstructorArgs([_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_, true])
            ->onlyMethods(['getValue'])
            ->getMock();

        $spy->method('getValue')->willReturnCallback(
            function ($sql, $useCache = true) use ($realDb) {
                if (str_contains((string) $sql, 'cart_product')) {
                    ++$this->cartQuantityQueries;
                }

                return $realDb->getValue($sql, $useCache);
            }
        );

        Db::setInstanceForTesting($spy);
        $context = Context::getContext();
        foreach (['cart', 'container', 'currency', 'language', 'shop'] as $property) {
            $this->contextBackup[$property] = $context->$property;
        }
        Cache::clean('Product::getPriceStatic_*');
    }

    protected function tearDown(): void
    {
        Db::deleteTestingInstance();
        $context = Context::getContext();
        foreach ($this->contextBackup as $property => $value) {
            $context->$property = $value;
        }
        Cache::clean('Product::getPriceStatic_*');
        DatabaseDump::restoreTables(['cart', 'cart_cart_rule', 'cart_product', 'cart_rule', 'cart_rule_lang', 'cart_rule_shop', 'specific_price', 'specific_price_priority']);
        SpecificPrice::flushCache();

        parent::tearDown();
    }

    public function testItReadsTheCartQuantityOncePerProductAndCart(): void
    {
        $idCart = (int) Db::getInstance()->getValue('SELECT id_cart FROM ' . _DB_PREFIX_ . 'cart ORDER BY id_cart DESC');
        $idProduct = (int) Db::getInstance()->getValue('SELECT id_product FROM ' . _DB_PREFIX_ . 'product');
        $this->cartQuantityQueries = 0;

        // Several quantities, as a cart holding several combinations of one product produces.
        foreach ([1, 2, 3, 4, 5] as $quantity) {
            Product::getPriceStatic($idProduct, true, null, 6, null, false, true, $quantity, false, null, $idCart, 0);
        }

        $this->assertSame(1, $this->cartQuantityQueries);
    }

    public function testDeletingAProductKeptAsAGiftDropsItsCartQuantity(): void
    {
        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;
        $context = Context::getContext();
        $context->container = self::getContainer();
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->shop = new Shop(1);
        $idProduct = (int) Db::getInstance()->getValue(
            'SELECT p.`id_product` FROM `' . _DB_PREFIX_ . 'product` p
             WHERE p.`cache_default_attribute` = 0 AND p.`active` = 1
             AND p.`id_product` NOT IN (SELECT `id_product` FROM `' . _DB_PREFIX_ . 'specific_price`)
             ORDER BY p.`id_product`'
        );
        $cart = new Cart();
        $cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_shop = 1;
        $cart->id_shop_group = 1;
        $cart->add();
        $context->cart = $cart;
        $cart->updateQty(2, $idProduct);

        $specificPrice = new SpecificPrice();
        $specificPrice->id_product = $idProduct;
        $specificPrice->id_product_attribute = 0;
        $specificPrice->id_shop = 0;
        $specificPrice->id_currency = 0;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->id_customer = 0;
        $specificPrice->price = -1;
        $specificPrice->from_quantity = 2;
        $specificPrice->reduction = 0.5;
        $specificPrice->reduction_type = 'percentage';
        $specificPrice->reduction_tax = 1;
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        $specificPrice->add();

        $cartRule = new CartRule();
        $cartRule->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Gift'];
        $cartRule->gift_product = $idProduct;
        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = 1;
        $cartRule->date_from = date('Y-m-d H:i:s', time() - 86400);
        $cartRule->date_to = date('Y-m-d H:i:s', time() + 86400);
        $cartRule->add();
        $cart->addCartRule((int) $cartRule->id);
        $cartQuantity = static fn (): int => (int) Db::getInstance()->getValue(
            'SELECT SUM(`quantity`) FROM `' . _DB_PREFIX_ . 'cart_product` WHERE `id_cart` = ' . (int) $cart->id . ' AND `id_product` = ' . $idProduct
        );
        self::assertSame(3, $cartQuantity(), 'two bought units and the gift');

        $price = static fn (?int $idCart): float => (float) Product::getPriceStatic($idProduct, false, 0, 6, null, false, true, 1, false, null, $idCart);
        $fullPrice = $price(null);
        self::assertLessThan($fullPrice, $price((int) $cart->id), 'the quantity discount applies to the cart');

        $cart->deleteProduct($idProduct);

        self::assertSame(1, $cartQuantity(), 'the gift is kept');
        self::assertEqualsWithDelta($fullPrice, $price((int) $cart->id), 0.000001, 'one unit left, the quantity discount no longer applies');
    }
}
