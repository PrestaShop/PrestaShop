<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes\Cart;

use Carrier;
use Cart;
use Configuration;
use Context;
use Country;
use Currency;
use Db;
use Language;
use Product;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tools;

/**
 * A product no carrier can deliver to the chosen address is given a placeholder carrier list, which makes
 * it group into a package of its own. One package is one order, so the cart is paid for as two orders.
 */
class UnshippablePackagingTest extends KernelTestCase
{
    private Carrier $carrier;

    private Product $shippable;

    private Product $unshippable;

    private Cart $cart;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        global $kernel;
        $kernel = self::$kernel;

        $context = Context::getContext();
        $context->currency = Currency::getDefaultCurrency();
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));

        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);

        // A carrier that serves no zone, so nothing it is restricted to can be delivered anywhere.
        $this->carrier = new Carrier();
        $this->carrier->name = 'unshippable packaging test';
        $this->carrier->delay = [(int) Configuration::get('PS_LANG_DEFAULT') => 'never'];
        $this->carrier->active = true;
        $this->carrier->shipping_method = Carrier::SHIPPING_METHOD_FREE;
        $this->carrier->range_behavior = 0;
        $this->carrier->add();
        Db::getInstance()->delete('carrier_zone', 'id_carrier = ' . (int) $this->carrier->id);

        $this->shippable = $this->createProduct('unshippable packaging shippable');
        $this->unshippable = $this->createProduct('unshippable packaging restricted');
        // Carrier::add() writes id_reference straight to the row and leaves the property alone, and after
        // an insert the reference is the id.
        Db::getInstance()->insert('product_carrier', [
            'id_product' => (int) $this->unshippable->id,
            'id_carrier_reference' => (int) $this->carrier->id,
            'id_shop' => 1,
        ]);

        $idCustomer = (int) Db::getInstance()->getValue(
            'SELECT id_customer FROM ' . _DB_PREFIX_ . 'customer WHERE deleted = 0 AND is_guest = 0 ORDER BY id_customer ASC'
        );
        $idAddress = (int) Db::getInstance()->getValue(
            'SELECT id_address FROM ' . _DB_PREFIX_ . 'address WHERE id_customer = ' . $idCustomer . ' AND deleted = 0 ORDER BY id_address ASC'
        );

        $this->cart = new Cart();
        $this->cart->id_customer = $idCustomer;
        $this->cart->id_address_delivery = $idAddress;
        $this->cart->id_address_invoice = $idAddress;
        $this->cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->cart->id_currency = (int) Currency::getDefaultCurrency()->id;
        $this->cart->id_shop = 1;
        $this->cart->add();
        $context->cart = $this->cart;

        $this->cart->updateQty(1, (int) $this->shippable->id);
        $this->cart->updateQty(1, (int) $this->unshippable->id);
    }

    protected function tearDown(): void
    {
        Db::getInstance()->delete('cart_product', 'id_cart = ' . (int) $this->cart->id);
        $this->cart->delete();
        Db::getInstance()->delete('product_carrier', 'id_product = ' . (int) $this->unshippable->id);
        foreach ([$this->shippable, $this->unshippable] as $product) {
            Db::getInstance()->delete('category_product', 'id_product = ' . (int) $product->id);
            $product->delete();
        }
        $this->carrier->delete();

        parent::tearDown();
    }

    public function testACartHoldingAnUndeliverableProductOffersNoPackageToShip(): void
    {
        $this->assertSame(
            [],
            Carrier::getAvailableCarrierList(new Product((int) $this->unshippable->id), 0, (int) $this->cart->id_address_delivery, null, $this->cart),
            'the fixture has to leave this product with no carrier at all'
        );

        $this->assertSame(0, $this->countPackages($this->cart), 'one package is one order');
        $this->assertSame([], $this->cart->getDeliveryOptionList(null, true));
    }

    /**
     * The same cart without that product is untouched: this must block the carts it has to, and no others.
     */
    public function testACartWhoseProductsCanAllBeDeliveredIsUnaffected(): void
    {
        $this->cart->deleteProduct((int) $this->unshippable->id);

        $this->assertSame(1, $this->countPackages($this->cart));
        $this->assertNotSame([], $this->cart->getDeliveryOptionList(null, true));
    }

    /**
     * A virtual product has no carrier by nature. It has always been packaged through the placeholder and
     * has to stay orderable.
     */
    public function testAVirtualProductIsStillPackaged(): void
    {
        $this->cart->deleteProduct((int) $this->unshippable->id);

        $virtual = $this->createProduct('unshippable packaging virtual');
        $virtual->is_virtual = true;
        $virtual->product_type = 'virtual';
        $virtual->update();
        $this->cart->updateQty(1, (int) $virtual->id);

        $packages = $this->countPackages($this->cart);

        $this->cart->deleteProduct((int) $virtual->id);
        Db::getInstance()->delete('category_product', 'id_product = ' . (int) $virtual->id);
        $virtual->delete();

        $this->assertSame(1, $packages);
    }

    private function countPackages(Cart $cart): int
    {
        $packages = 0;
        foreach ($cart->getPackageList(true) as $byAddress) {
            $packages += count($byAddress);
        }

        return $packages;
    }

    private function createProduct(string $name): Product
    {
        $product = new Product();
        $product->id_category_default = 2;
        $product->name = [(int) Configuration::get('PS_LANG_DEFAULT') => $name];
        $product->link_rewrite = [(int) Configuration::get('PS_LANG_DEFAULT') => Tools::str2url($name)];
        $product->price = 10;
        $product->id_tax_rules_group = 1;
        $product->add();
        $product->addToCategories([2]);
        StockAvailable::setQuantity((int) $product->id, 0, 100);

        return $product;
    }
}
