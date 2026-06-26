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
use Category;
use Configuration;
use Context;
use Currency;
use DateTime;
use Db;
use Product;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tax;
use TaxRule;
use TaxRulesGroup;
use Tools;

/**
 * A fixed-amount cart rule restricted to specific products (here a category) must never discount
 * more than the total of the eligible products, even when the cart also contains products outside
 * the restriction. Otherwise part of the fixed amount spills onto non-eligible products.
 *
 * @see https://github.com/PrestaShop/PrestaShop/issues/41853
 */
class CartRuleProductRestrictionCapTest extends KernelTestCase
{
    private static int $idAddress;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        // Cart-rule processing reaches the service container through the legacy Context.
        Context::getContext()->container = self::getContainer();
        Context::getContext()->currency = Currency::getDefaultCurrency();

        Configuration::loadConfiguration();
        Configuration::updateValue('PS_TAX_ADDRESS_TYPE', 'id_address_invoice');
        Configuration::updateValue('PS_ORDER_OUT_OF_STOCK', true);
        Configuration::set('PS_PRICE_DISPLAY_PRECISION', 2);
        Configuration::set('PS_CART_RULE_FEATURE_ACTIVE', true);
        Configuration::set('PS_GROUP_FEATURE_ACTIVE', true);
        // Pre-existing cart rules must not interfere.
        Db::getInstance()->execute('UPDATE ' . _DB_PREFIX_ . 'cart_rule SET active = 0');

        self::$idAddress = (int) self::makeAddress()->id;
    }

    public function testFixedAmountRestrictedToCategoryIsCappedByEligibleProductsTotal(): void
    {
        $idTaxRulesGroup = $this->getIdTaxRulesGroup(20);

        // A dedicated category holding a single eligible product priced 9.
        $category = new Category(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $category->name = 'Restricted ' . uniqid();
        $category->link_rewrite = Tools::str2url($category->name);
        $category->id_parent = (int) Configuration::get('PS_HOME_CATEGORY');
        $this->assertTrue((bool) $category->add());

        $eligibleProduct = $this->makeProduct('Eligible ' . uniqid(), 9, $idTaxRulesGroup);
        $eligibleProduct->addToCategories([(int) $category->id]);
        $nonEligibleProduct = $this->makeProduct('NonEligible ' . uniqid(), 50, $idTaxRulesGroup);

        // Fixed amount 10 cart rule (tax excluded), restricted to the category above.
        $cartRule = new CartRule(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $cartRule->name = 'Fixed 10 restricted to category';
        $dateFrom = (new DateTime())->modify('-2 days');
        $dateTo = (new DateTime())->modify('+2 days');
        $cartRule->date_from = $dateFrom->format('Y-m-d H:i:s');
        $cartRule->date_to = $dateTo->format('Y-m-d H:i:s');
        $cartRule->quantity = 1;
        $cartRule->quantity_per_user = 1;
        $cartRule->reduction_amount = 10;
        $cartRule->reduction_tax = false;
        $cartRule->product_restriction = true;
        $this->assertTrue((bool) $cartRule->save());

        // Restrict the rule to our category.
        Db::getInstance()->insert('cart_rule_product_rule_group', ['id_cart_rule' => (int) $cartRule->id, 'quantity' => 1]);
        $idProductRuleGroup = (int) Db::getInstance()->Insert_ID();
        Db::getInstance()->insert('cart_rule_product_rule', ['id_product_rule_group' => $idProductRuleGroup, 'type' => 'categories']);
        $idProductRule = (int) Db::getInstance()->Insert_ID();
        Db::getInstance()->insert('cart_rule_product_rule_value', ['id_product_rule' => $idProductRule, 'id_item' => (int) $category->id]);

        $cart = $this->makeCart();
        $cart->updateQty(1, (int) $eligibleProduct->id);
        $cart->updateQty(1, (int) $nonEligibleProduct->id);
        $cart->addCartRule((int) $cartRule->id);

        // The eligible products only total 9 (tax excl.), so the 10 fixed amount must be capped at 9
        // and must not spill onto the 50 non-eligible product.
        // Tax excluded (reduction_tax == use_tax => branch "reduction_tax == use_tax") and tax
        // included (reduction_tax != use_tax => the other branch) both read getOrderTotal(ONLY_DISCOUNTS)
        // and must respect the cap: 9 and 9 * 1.20 = 10.8.
        $this->assertEquals(9, $cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS));
        $this->assertEquals(10.8, $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS));
    }

    private function makeProduct(string $name, float $price, int $idTaxRulesGroup): Product
    {
        $product = new Product(null, false, (int) Configuration::get('PS_LANG_DEFAULT'));
        $product->id_tax_rules_group = $idTaxRulesGroup;
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

    private function getIdTaxRulesGroup(int $rate): int
    {
        $tax = new Tax(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $tax->name = $rate . '% TAX ' . uniqid();
        $tax->rate = $rate;
        $tax->active = true;
        $tax->save();

        $taxRulesGroup = new TaxRulesGroup(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $taxRulesGroup->name = $rate . '% TRG ' . uniqid();
        $taxRulesGroup->active = true;
        $taxRulesGroup->save();

        $taxRule = new TaxRule(null, (int) Configuration::get('PS_LANG_DEFAULT'));
        $taxRule->id_tax = (int) $tax->id;
        $taxRule->id_country = (int) Configuration::get('PS_COUNTRY_DEFAULT');
        $taxRule->id_tax_rules_group = (int) $taxRulesGroup->id;
        $taxRule->save();

        return (int) $taxRulesGroup->id;
    }
}
