<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Cart;
use CartRule;
use Configuration;
use Context;
use Country;
use Currency;
use Customer;
use Db;
use Product;
use SpecificPrice;
use StockAvailable;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tools;

/**
 * A percentage cart rule restricted to a product must be computed on what the customer actually pays for
 * that product. When the product also carries a specific price, the percentage was taken from the catalog
 * price instead, so the discount line was larger than the product it discounted.
 */
class CartRulePercentOnDiscountedPriceTest extends KernelTestCase
{
    private const NAME = 'CartRulePercentOnDiscountedPrice-33523';
    private const CATALOG_PRICE = 200.0;
    private const SPECIFIC_PRICE_REDUCTION = 0.30;
    private const CART_RULE_PERCENT = 50.0;

    /** @var CartRule */
    private $cartRule;
    /** @var Cart */
    private $cart;
    /** @var int */
    private $idLang;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->idLang = (int) Configuration::get('PS_LANG_DEFAULT');

        $context = Context::getContext();
        $context->container = self::getContainer();
        $context->currency = new Currency(Currency::getDefaultCurrencyId());
        $context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $context->customer = new Customer((int) $this->getCustomerId());

        self::removeFixture();
        $this->buildFixture();
    }

    protected function tearDown(): void
    {
        self::removeFixture();

        parent::tearDown();
    }

    /**
     * The specific price is what makes this case different from an ordinary one.
     */
    public function testTheProductIsSoldAtItsSpecificPrice(): void
    {
        $products = $this->cart->getProducts(true);
        self::assertCount(1, $products);

        self::assertEqualsWithDelta(
            self::CATALOG_PRICE * (1 - self::SPECIFIC_PRICE_REDUCTION),
            (float) $products[0]['price_with_reduction_without_tax'],
            0.01,
            'the cart sells the product at its reduced price'
        );
    }

    /**
     * The reported bug, which only ever showed on the tax included side: a cart row's `price` is already
     * the reduced one, so the tax excluded branch was right, while the tax included branch read
     * `price_without_reduction` and took the percentage from the catalog price.
     *
     * @dataProvider bothTaxModes
     */
    public function testThePercentageIsTakenFromThePriceActuallyPaid(bool $useTax): void
    {
        $paid = self::CATALOG_PRICE * (1 - self::SPECIFIC_PRICE_REDUCTION);

        self::assertEqualsWithDelta(
            $paid * self::CART_RULE_PERCENT / 100,
            (float) $this->cartRule->getContextualValue($useTax, Context::getContext()),
            0.01,
            'the discount must not exceed a percentage of what the product costs in this cart'
        );
    }

    public static function bothTaxModes(): array
    {
        return ['tax excluded' => [false], 'tax included' => [true]];
    }

    /**
     * The discount can never be worth more than the product it applies to.
     */
    public function testTheDiscountStaysBelowTheProductTotal(): void
    {
        $products = $this->cart->getProducts(true);
        $lineTotal = (float) $products[0]['price_with_reduction_without_tax'] * (int) $products[0]['cart_quantity'];

        self::assertLessThan(
            $lineTotal,
            (float) $this->cartRule->getContextualValue(true, Context::getContext()),
            'a 50% discount cannot be worth more than half the line it applies to'
        );
    }

    private function buildFixture(): void
    {
        $product = new Product(null, false, $this->idLang);
        $product->name = self::NAME;
        $product->link_rewrite = Tools::str2url(self::NAME);
        $product->price = self::CATALOG_PRICE;
        // No tax rules group so the arithmetic under test is not mixed with tax rounding.
        $product->id_tax_rules_group = 0;
        $product->active = true;
        $product->visibility = 'both';
        $product->id_category_default = (int) Configuration::get('PS_HOME_CATEGORY');
        $product->save();
        $product->addToCategories([(int) Configuration::get('PS_HOME_CATEGORY')]);
        StockAvailable::setQuantity((int) $product->id, 0, 100, null, false);

        $specificPrice = new SpecificPrice();
        $specificPrice->id_product = (int) $product->id;
        $specificPrice->id_shop = 0;
        $specificPrice->id_currency = 0;
        $specificPrice->id_country = 0;
        $specificPrice->id_group = 0;
        $specificPrice->id_customer = 0;
        $specificPrice->id_product_attribute = 0;
        $specificPrice->from_quantity = 1;
        $specificPrice->price = -1;
        $specificPrice->reduction = self::SPECIFIC_PRICE_REDUCTION;
        $specificPrice->reduction_tax = 1;
        $specificPrice->reduction_type = 'percentage';
        $specificPrice->from = '0000-00-00 00:00:00';
        $specificPrice->to = '0000-00-00 00:00:00';
        $specificPrice->save();

        $cartRule = new CartRule(null, $this->idLang);
        $cartRule->name = self::NAME;
        $cartRule->date_from = date('Y-m-d H:i:s', strtotime('-1 day'));
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+1 day'));
        $cartRule->quantity = 10;
        $cartRule->quantity_per_user = 10;
        $cartRule->reduction_percent = self::CART_RULE_PERCENT;
        // -2 is "discount on the selection of products", the mode the report uses: the percentage is
        // taken from the selected products rather than from the whole cart.
        $cartRule->reduction_product = -2;
        $cartRule->product_restriction = true;
        $cartRule->active = true;
        $cartRule->save();
        $this->restrictRuleToProduct($cartRule, $product);
        $this->cartRule = new CartRule((int) $cartRule->id, $this->idLang);

        $cart = new Cart(null, $this->idLang);
        $cart->id_customer = (int) $this->getCustomerId();
        $cart->id_currency = Currency::getDefaultCurrencyId();
        $cart->id_address_delivery = (int) $this->getAddressId();
        $cart->id_address_invoice = (int) $this->getAddressId();
        $cart->save();
        Context::getContext()->cart = $cart;
        $cart->updateQty(1, (int) $product->id);

        $this->cart = new Cart((int) $cart->id, $this->idLang);
        Context::getContext()->cart = $this->cart;
        $this->cart->addCartRule((int) $cartRule->id);
    }

    private function restrictRuleToProduct(CartRule $cartRule, Product $product): void
    {
        $db = Db::getInstance();
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_group (id_cart_rule, quantity) VALUES (' . (int) $cartRule->id . ', 1)');
        $idGroup = (int) $db->Insert_ID();
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . "cart_rule_product_rule (id_product_rule_group, type) VALUES ($idGroup, 'products')");
        $idRule = (int) $db->Insert_ID();
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . "cart_rule_product_rule_value (id_product_rule, id_item) VALUES ($idRule, " . (int) $product->id . ')');
    }

    private function getCustomerId(): int
    {
        return (int) Db::getInstance()->getValue('SELECT id_customer FROM ' . _DB_PREFIX_ . 'customer WHERE deleted = 0 ORDER BY id_customer DESC');
    }

    private function getAddressId(): int
    {
        return (int) Db::getInstance()->getValue('SELECT id_address FROM ' . _DB_PREFIX_ . 'address WHERE deleted = 0 ORDER BY id_address DESC');
    }

    private static function removeFixture(): void
    {
        $db = Db::getInstance();

        foreach ($db->executeS('SELECT DISTINCT cr.id_cart_rule FROM ' . _DB_PREFIX_ . 'cart_rule cr
            JOIN ' . _DB_PREFIX_ . 'cart_rule_lang crl ON crl.id_cart_rule = cr.id_cart_rule
            WHERE crl.name = "' . self::NAME . '"') as $row) {
            $id = (int) $row['id_cart_rule'];
            foreach ($db->executeS('SELECT id_product_rule_group FROM ' . _DB_PREFIX_ . 'cart_rule_product_rule_group WHERE id_cart_rule = ' . $id) as $group) {
                $idGroup = (int) $group['id_product_rule_group'];
                foreach ($db->executeS('SELECT id_product_rule FROM ' . _DB_PREFIX_ . 'cart_rule_product_rule WHERE id_product_rule_group = ' . $idGroup) as $rule) {
                    $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart_rule_product_rule_value WHERE id_product_rule = ' . (int) $rule['id_product_rule']);
                }
                $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart_rule_product_rule WHERE id_product_rule_group = ' . $idGroup);
                $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart_rule_product_rule_group WHERE id_product_rule_group = ' . $idGroup);
            }
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart_cart_rule WHERE id_cart_rule = ' . $id);
            (new CartRule($id))->delete();
        }

        foreach ($db->executeS('SELECT DISTINCT p.id_product FROM ' . _DB_PREFIX_ . 'product p
            JOIN ' . _DB_PREFIX_ . 'product_lang pl ON pl.id_product = p.id_product
            WHERE pl.name = "' . self::NAME . '"') as $row) {
            $id = (int) $row['id_product'];
            $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'specific_price WHERE id_product = ' . $id);
            foreach ($db->executeS('SELECT DISTINCT id_cart FROM ' . _DB_PREFIX_ . 'cart_product WHERE id_product = ' . $id) as $cartRow) {
                $idCart = (int) $cartRow['id_cart'];
                $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart_product WHERE id_cart = ' . $idCart);
                $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart_cart_rule WHERE id_cart = ' . $idCart);
                $db->execute('DELETE FROM ' . _DB_PREFIX_ . 'cart WHERE id_cart = ' . $idCart);
            }
            (new Product($id))->delete();
        }
    }
}
