<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Address;
use Cart;
use CartRule;
use Configuration;
use Context;
use Currency;
use Db;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tools;

/**
 * Adding a product to the cart must not loop forever when two automatic discounts are mutually
 * incompatible. A free-gift discount and a cart-level discount that are not compatible with each
 * other used to make Cart::updateQty() recurse endlessly (auto-add -> compatibility removal ->
 * gift product deletion -> auto-add -> ...), freezing the front office add-to-cart request.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/41671
 */
class CartRuleIncompatibleAutoDiscountLoopTest extends KernelTestCase
{
    private static int $idAddress;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        // Cart-rule processing and the discount compatibility service reach the container through the legacy Context.
        Context::getContext()->container = self::getContainer();
        Context::getContext()->currency = Currency::getDefaultCurrency();

        Configuration::loadConfiguration();
        Configuration::updateValue('PS_TAX_ADDRESS_TYPE', 'id_address_invoice');
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);
        Configuration::set('PS_CART_RULE_FEATURE_ACTIVE', true);
        Configuration::set('PS_GROUP_FEATURE_ACTIVE', true);

        // The new discount system (and its compatibility check) is only active behind this feature flag.
        Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . "feature_flag SET state = 1 WHERE name = 'discount'");
        // Pre-existing cart rules must not interfere.
        Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'cart_rule SET active = 0');

        self::$idAddress = (int) self::makeAddress()->id;
    }

    public function testAddingProductWithIncompatibleAutomaticDiscountsDoesNotLoop(): void
    {
        $idLang = (int) Configuration::get('PS_LANG_DEFAULT');
        $giftProduct = $this->makeProduct('Gift ' . uniqid(), 25);
        $boughtProduct = $this->makeProduct('Bought ' . uniqid(), 100);

        // An automatic (codeless) free-gift discount giving away the gift product.
        $freeGift = $this->makeAutomaticCartRule('Auto free gift', $this->getCartRuleTypeId('free_gift'));
        $freeGift->gift_product = (int) $giftProduct->id;
        $this->assertTrue((bool) $freeGift->save());

        // An automatic (codeless) cart-level percentage discount, NOT marked compatible with the free gift.
        $cartLevel = $this->makeAutomaticCartRule('Auto cart 10%', $this->getCartRuleTypeId('cart_level'));
        $cartLevel->reduction_percent = 10;
        $this->assertTrue((bool) $cartLevel->save());

        $cart = $this->makeCart();

        // Before the fix this call never returned (infinite auto-add/auto-remove recursion).
        $result = $cart->updateQty(1, (int) $boughtProduct->id, 0, false, 'up', 0, null, true);

        $this->assertTrue((bool) $result, 'updateQty must complete and succeed');

        // The bought product is in the cart...
        $productIds = array_column($cart->getProducts(true), 'id_product');
        $this->assertContains((int) $boughtProduct->id, array_map('intval', $productIds));

        // ...and the incompatible discounts resolved to a single applied cart rule (no endless re-adding).
        $appliedRuleIds = Db::getInstance()->executeS(
            'SELECT id_cart_rule FROM ' . _DB_PREFIX_ . 'cart_cart_rule WHERE id_cart = ' . (int) $cart->id
        );
        $this->assertLessThanOrEqual(1, count($appliedRuleIds), 'Incompatible auto discounts must not both stay applied');
    }

    private function makeAutomaticCartRule(string $name, int $idCartRuleType): CartRule
    {
        $cartRule = new CartRule(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $cartRule->name = $name;
        $cartRule->date_from = date('Y-m-d H:i:s', strtotime('-2 days'));
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+2 days'));
        $cartRule->quantity = 1000;
        $cartRule->quantity_per_user = 0;
        $cartRule->code = ''; // automatic
        $cartRule->active = true;
        $cartRule->id_cart_rule_type = $idCartRuleType;

        return $cartRule;
    }

    private function getCartRuleTypeId(string $discountType): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT id_cart_rule_type FROM ' . _DB_PREFIX_ . "cart_rule_type WHERE discount_type = '" . pSQL($discountType) . "'"
        );
    }

    private function makeProduct(string $name, float $price): Product
    {
        $product = new Product(null, false, (int) Configuration::get('PS_LANG_DEFAULT'));
        $product->name = $name;
        $product->price = $price;
        $product->link_rewrite = Tools::str2url($name);
        $this->assertTrue((bool) $product->save());

        return $product;
    }

    private function makeCart(): Cart
    {
        $cart = new Cart(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $cart->id_currency = (int) Currency::getDefaultCurrencyId();
        $cart->id_address_invoice = self::$idAddress;
        $this->assertTrue((bool) $cart->save());
        Context::getContext()->cart = $cart;

        return $cart;
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
}
