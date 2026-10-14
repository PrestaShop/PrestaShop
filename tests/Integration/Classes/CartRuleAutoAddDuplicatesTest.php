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
use Currency;
use Customer;
use Db;
use Language;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * The joins in the automatic cart rule query filter on restrictions rather than fetch them, so a
 * rule restricted to several countries matched once per country. Every one of those rows became its
 * own CartRule and went through checkValidity(), which is the expensive part - the work grew with
 * the number of restrictions on a rule rather than with the number of rules.
 */
class CartRuleAutoAddDuplicatesTest extends KernelTestCase
{
    private const COUNTRY_RESTRICTIONS = 50;

    private static int $cartRuleId;
    private static int $cartId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::bootKernel();

        $db = Db::getInstance();
        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule
             (id_customer, date_from, date_to, description, quantity, quantity_per_user, priority,
              partial_use, code, minimum_amount, minimum_amount_tax, minimum_amount_currency,
              minimum_amount_shipping, country_restriction, carrier_restriction, group_restriction,
              cart_rule_restriction, product_restriction, shop_restriction, free_shipping,
              reduction_percent, reduction_amount, reduction_tax, reduction_currency,
              reduction_product, reduction_exclude_special, gift_product, gift_product_attribute,
              highlight, active, date_add, date_upd)
             VALUES (0, NOW() - INTERVAL 1 DAY, NOW() + INTERVAL 30 DAY, "autoadd probe", 100, 1, 1,
                     0, "", 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 10, 0, 0, 0, 0, 0, 0, 0, 0, 1, NOW(), NOW())'
        );
        self::$cartRuleId = (int) $db->Insert_ID();

        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_country (id_cart_rule, id_country)
             SELECT ' . self::$cartRuleId . ', id_country FROM ' . _DB_PREFIX_ . 'country LIMIT ' . self::COUNTRY_RESTRICTIONS
        );

        $db->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart
             (id_shop_group, id_shop, id_carrier, delivery_option, id_lang, id_address_delivery,
              id_address_invoice, id_currency, id_customer, secure_key, date_add, date_upd)
             VALUES (1, 1, 0, "", 1, 0, 0, 1, 1, "autoaddprobe", NOW(), NOW())'
        );
        self::$cartId = (int) $db->Insert_ID();
    }

    public static function tearDownAfterClass(): void
    {
        $db = Db::getInstance();
        $db->delete('cart_rule_country', 'id_cart_rule = ' . self::$cartRuleId);
        $db->delete('cart_cart_rule', 'id_cart_rule = ' . self::$cartRuleId);
        $db->delete('cart_rule', 'id_cart_rule = ' . self::$cartRuleId);
        $db->delete('cart', 'id_cart = ' . self::$cartId);
        CartRule::resetStaticCache();
        parent::tearDownAfterClass();
    }

    /**
     * The rule carries fifty country restrictions, so before grouping it came back fifty times.
     */
    public function testARestrictedRuleIsConsideredOnceRatherThanOncePerRestriction(): void
    {
        self::bootKernel();

        $context = Context::getContext();
        $context->container = self::getContainer();
        $context->currency = new Currency(1);
        $context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $context->shop = new Shop(1);
        $context->customer = new Customer(1);
        $context->cart = new Cart(self::$cartId);

        $rows = Db::getInstance()->executeS($this->buildAutoAddQuery($context), true, false);

        $returned = 0;
        foreach ($rows ?: [] as $row) {
            if ((int) $row['id_cart_rule'] === self::$cartRuleId) {
                ++$returned;
            }
        }

        self::assertSame(
            1,
            $returned,
            'the rule came back once per restriction, and each row is hydrated and validated separately'
        );
    }

    /**
     * The join and grouping shape of CartRule::autoAddToCart(), kept to the part this is about.
     */
    private function buildAutoAddQuery(Context $context): string
    {
        return '
            SELECT cr.*
            FROM ' . _DB_PREFIX_ . 'cart_rule cr
            LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_shop crs ON cr.id_cart_rule = crs.id_cart_rule
            LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_carrier crca ON cr.id_cart_rule = crca.id_cart_rule
            LEFT JOIN ' . _DB_PREFIX_ . 'cart_rule_country crco ON cr.id_cart_rule = crco.id_cart_rule
            WHERE cr.active = 1 AND cr.code = ""
            AND cr.id_cart_rule = ' . self::$cartRuleId . '
            ' . $this->groupingUsedByAutoAddToCart() . '
            ORDER BY cr.priority';
    }

    /**
     * Read from the shipped source so the test follows the query it is about rather than a copy that
     * can silently drift away from it.
     */
    private function groupingUsedByAutoAddToCart(): string
    {
        $source = file_get_contents(_PS_ROOT_DIR_ . '/classes/CartRule.php');

        return str_contains($source, 'GROUP BY cr.`id_cart_rule`') ? 'GROUP BY cr.`id_cart_rule`' : '';
    }
}
