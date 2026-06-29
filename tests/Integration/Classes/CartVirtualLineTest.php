<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Address;
use Cart;
use Combination;
use Configuration;
use Context;
use Currency;
use PHPUnit\Framework\TestCase;
use Product;
use ProductDownload;
use Tools;

/**
 * Covers the effective per-line virtuality resolution introduced for virtual combinations:
 * a cart/order line is virtual when its chosen combination is flagged is_virtual, otherwise
 * it follows the product's is_virtual flag.
 *
 * @group virtual-combinations
 */
class CartVirtualLineTest extends TestCase
{
    /**
     * @var int
     */
    private static $idAddress;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Configuration::loadConfiguration();
        Configuration::updateValue('PS_TAX_ADDRESS_TYPE', 'id_address_invoice');
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);

        self::$idAddress = self::makeAddress()->id;
    }

    /**
     * Mixed cart: one virtual combination + one physical combination of the same product.
     * The cart must NOT be considered fully virtual (it still needs shipping).
     */
    public function testMixedCombinationCartIsNotVirtual(): void
    {
        if (!ProductDownload::isFeatureActive()) {
            // isVirtualCart() short-circuits to false when the virtual-product feature is off,
            // which would make this assertion meaningless.
            self::markTestSkipped('Virtual product feature (ProductDownload) is not active.');
        }

        $product = self::makeProductWithCombinations();
        $idVirtual = self::addCombination($product, 'virtual', true);
        $idPhysical = self::addCombination($product, 'physical', false);

        $cart = self::makeCart();
        self::assertEquals(true, $cart->updateQty(1, $product->id, $idVirtual));
        self::assertEquals(true, $cart->updateQty(1, $product->id, $idPhysical));

        // Effective per-line is_virtual: one line virtual, one physical => not a virtual cart.
        self::assertFalse($cart->isVirtualCart());
        self::assertTrue($cart->hasRealProducts());

        // The projection returns the effective per-line flag.
        $lines = $cart->getProducts(true);
        $byAttribute = [];
        foreach ($lines as $line) {
            $byAttribute[(int) $line['id_product_attribute']] = (int) $line['is_virtual'];
        }
        self::assertSame(1, $byAttribute[$idVirtual]);
        self::assertSame(0, $byAttribute[$idPhysical]);
    }

    /**
     * Cart containing only the virtual combination must be considered fully virtual (no shipping).
     */
    public function testOnlyVirtualCombinationCartIsVirtual(): void
    {
        if (!ProductDownload::isFeatureActive()) {
            self::markTestSkipped('Virtual product feature (ProductDownload) is not active.');
        }

        $product = self::makeProductWithCombinations();
        $idVirtual = self::addCombination($product, 'virtual-only', true);

        $cart = self::makeCart();
        self::assertEquals(true, $cart->updateQty(1, $product->id, $idVirtual));

        self::assertTrue($cart->isVirtualCart());
        self::assertFalse($cart->hasRealProducts());
    }

    private static function makeProductWithCombinations(): Product
    {
        $name = 'combi-product-' . microtime(true) . '-' . getmypid();
        $product = new Product(null, false, (int) Configuration::get('PS_LANG_DEFAULT'));
        $product->name = $name;
        $product->price = 10.0;
        $product->link_rewrite = Tools::str2url($name);
        // Product-level is_virtual stays 0: virtuality is decided per combination.
        $product->is_virtual = 0;
        self::assertTrue($product->save());

        return $product;
    }

    private static function addCombination(Product $product, string $reference, bool $isVirtual): int
    {
        $combination = new Combination();
        $combination->id_product = (int) $product->id;
        $combination->reference = $reference . '-' . getmypid();
        $combination->weight = 1.5;
        $combination->is_virtual = $isVirtual;
        self::assertTrue($combination->save());

        return (int) $combination->id;
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
        self::assertTrue($address->save());

        return $address;
    }

    private static function makeCart(): Cart
    {
        $cart = new Cart(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $cart->id_currency = Currency::getDefaultCurrencyId();
        $cart->id_address_invoice = self::$idAddress;
        $cart->id_address_delivery = self::$idAddress;
        self::assertTrue($cart->save());
        Context::getContext()->cart = $cart;

        return $cart;
    }
}
