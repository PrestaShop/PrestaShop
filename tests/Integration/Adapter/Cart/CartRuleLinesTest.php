<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Adapter\Cart;

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
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\Query\GetCartForViewing;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartForOrderCreation;
use PrestaShop\PrestaShop\Core\Domain\Cart\QueryResult\CartView;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

/**
 * The discount lines of the back office cart views add up to the discount Cart::getOrderTotal() charges.
 */
class CartRuleLinesTest extends KernelTestCase
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

    public function testTheOrderCreationLinesAddUpToTheDiscountCharged(): void
    {
        $cart = $this->buildCart(true);

        self::assertEqualsWithDelta(
            (float) $cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS),
            array_sum($this->getOrderCreationLines($cart)),
            0.01
        );
    }

    public function testTheCartViewLinesAddUpToTheDiscountCharged(): void
    {
        $cart = $this->buildCart(true);

        self::assertEqualsWithDelta(
            (float) $cart->getOrderTotal(true, Cart::ONLY_DISCOUNTS),
            array_sum($this->getCartViewLines($cart)),
            0.01
        );
    }

    public function testAPercentageOnItsOwnKeepsItsValue(): void
    {
        $cart = $this->buildCart(false);

        self::assertEqualsWithDelta(
            [(float) $cart->getOrderTotal(false, Cart::ONLY_DISCOUNTS)],
            $this->getOrderCreationLines($cart),
            0.01
        );
    }

    /**
     * @return float[]
     */
    private function getOrderCreationLines(Cart $cart): array
    {
        Cache::clean('getContextualValue_*');
        /** @var CartForOrderCreation $result */
        $result = self::getContainer()->get('prestashop.core.query_bus')->handle(
            (new GetCartForOrderCreation((int) $cart->id))->setHideDiscounts(true)
        );

        return array_values(array_map(static fn ($cartRule): float => (float) $cartRule->getValue(), $result->getCartRules()));
    }

    /**
     * @return float[]
     */
    private function getCartViewLines(Cart $cart): array
    {
        Cache::clean('getContextualValue_*');
        /** @var CartView $result */
        $result = self::getContainer()->get('prestashop.core.query_bus')->handle(new GetCartForViewing((int) $cart->id));

        return array_map(
            static fn (array $cartRule): float => (float) preg_replace('/[^0-9.]/', '', $cartRule['formatted_value']),
            $result->getCartSummary()['cart_rules']
        );
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
