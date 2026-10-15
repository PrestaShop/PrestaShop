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
use Country;
use Currency;
use Customer;
use Db;
use Language;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The discount lines printed under the cart have to add up to the discount the cart charges. The
 * lines come from CartRule::getContextualValue() and the total from Cart::getOrderTotal(), and the
 * two only agree if a percentage restricted to a product accounts for what earlier rules already
 * took off that product.
 */
class CartRuleDisplayedValueTest extends KernelTestCase
{
    private const RESTRICTED_PRODUCT_ID = 2;
    private const SECOND_PRODUCT_ID = 3;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::resetDatabase();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        static::resetDatabase();
    }

    protected static function resetDatabase(): void
    {
        DatabaseDump::restoreTables([
            'cart',
            'cart_product',
            'cart_cart_rule',
            'cart_rule',
            'cart_rule_lang',
            'cart_rule_shop',
            'cart_rule_product_rule_group',
            'cart_rule_product_rule',
            'cart_rule_product_rule_value',
        ]);
    }

    public function testTheLinesShownAddUpToTheDiscountCharged(): void
    {
        $cart = $this->buildCart(true);

        self::assertEqualsWithDelta(
            (float) $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS),
            $this->sumOfShownLines($cart, 2),
            0.01,
            'the discount lines shown under the cart do not add up to the discount it charges'
        );
    }

    /**
     * With nothing applied before it, a percentage on a product is worth the whole percentage of
     * that product - the reduction above must not change that.
     */
    public function testAPercentageOnItsOwnIsUntouched(): void
    {
        $cart = $this->buildCart(false);

        self::assertEqualsWithDelta(
            (float) $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS),
            $this->sumOfShownLines($cart, 1),
            0.01,
            'a percentage applied on its own changed value'
        );
    }

    private function sumOfShownLines(Cart $cart, int $expectedRules): float
    {
        // Building the cart warmed getContextualValue()'s cache. A customer opening the cart gets a
        // fresh request instead, so drop it before reading what the page would print.
        Cache::clean('getContextualValue_*');

        $shownTotal = 0.0;
        $seen = [];
        foreach ($cart->getCartRules() as $cartRule) {
            if (isset($seen[$cartRule['id_cart_rule']])) {
                continue;
            }
            $seen[$cartRule['id_cart_rule']] = true;
            $shownTotal += (float) $cartRule['value_real'];
        }

        self::assertCount($expectedRules, $seen, 'the expected rules did not all apply');

        return $shownTotal;
    }

    private function buildCart(bool $withAmountOnTotal): Cart
    {
        self::bootKernel();
        $context = Context::getContext();
        $context->container = self::getContainer();
        // The CLI context has no shop front, and pricing reaches for the currency's precision.
        $context->currency = new Currency((int) Configuration::get('PS_CURRENCY_DEFAULT'));
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->shop = new Shop(1);
        $context->country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));
        $context->customer = new Customer(1);

        $this->deleteExistingCartRules();
        if ($withAmountOnTotal) {
            $this->createRule('Amount off the order total', ['reduction_amount' => 10.0]);
        }
        $this->createPercentOnOneProductRule();

        $cart = new Cart();
        $cart->id_customer = 1;
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $cart->id_shop = 1;
        $cart->id_guest = 1;
        $cart->add();

        $context->cart = $cart;
        $cart->updateQty(1, self::RESTRICTED_PRODUCT_ID);
        $cart->updateQty(1, self::SECOND_PRODUCT_ID);
        CartRule::autoAddToCart($context, false);

        return $cart;
    }

    private function deleteExistingCartRules(): void
    {
        $tables = [
            'cart_cart_rule',
            'cart_rule',
            'cart_rule_lang',
            'cart_rule_shop',
            'cart_rule_product_rule_group',
            'cart_rule_product_rule',
            'cart_rule_product_rule_value',
        ];
        foreach ($tables as $table) {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . $table);
        }
    }

    private function createPercentOnOneProductRule(): void
    {
        $id = $this->createRule('Percentage on one product', [
            'reduction_percent' => 10.0,
            'reduction_product' => self::RESTRICTED_PRODUCT_ID,
        ]);

        $db = Db::getInstance();
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_group (id_cart_rule, quantity) VALUES (' . $id . ', 1)');
        $groupId = (int) $db->Insert_ID();
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . "cart_rule_product_rule (id_product_rule_group, type) VALUES ($groupId, 'products')");
        $productRuleId = (int) $db->Insert_ID();
        $db->execute('INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_product_rule_value (id_product_rule, id_item) VALUES (' . $productRuleId . ', ' . self::RESTRICTED_PRODUCT_ID . ')');
        $db->execute('UPDATE ' . _DB_PREFIX_ . 'cart_rule SET product_restriction = 1 WHERE id_cart_rule = ' . $id);
    }

    /**
     * @param array<string, float|int> $reduction
     */
    private function createRule(string $name, array $reduction): int
    {
        $cartRule = new CartRule();
        // No code, so it applies on its own and the cart prints a line for it.
        $cartRule->name = [(int) Configuration::get('PS_LANG_DEFAULT') => $name];
        $cartRule->quantity = 100;
        $cartRule->quantity_per_user = 100;
        $cartRule->date_from = date('Y-m-d H:i:s', strtotime('-1 day'));
        $cartRule->date_to = date('Y-m-d H:i:s', strtotime('+1 year'));
        $cartRule->active = true;
        $cartRule->partial_use = true;
        $cartRule->priority = 1;
        $cartRule->reduction_tax = true;
        $cartRule->reduction_currency = (int) Configuration::get('PS_CURRENCY_DEFAULT');
        $cartRule->reduction_amount = $reduction['reduction_amount'] ?? 0;
        $cartRule->reduction_percent = $reduction['reduction_percent'] ?? 0;
        $cartRule->reduction_product = $reduction['reduction_product'] ?? 0;
        $cartRule->add();

        return (int) $cartRule->id;
    }
}
