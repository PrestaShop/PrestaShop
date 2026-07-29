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
use Customer;
use Db;
use DbPDO;
use Language;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

final class CartRuleAutoAddToCartTest extends KernelTestCase
{
    private const CUSTOMER_ID = 1;
    /** Address of customer 1, in country 8 */
    private const ADDRESS_ID = 1;
    private const CARRIER_ID = 2;

    private Context $context;

    private Db $originalDb;

    /**
     * Rows returned by the automatic cart rule query, which checkValidity() filters again afterwards
     *
     * @var DbPDO&object{queriedRuleIds: array<int, int[]>}
     */
    private DbPDO $db;

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();

        $this->context = Context::getContext();
        $this->context->container = self::getContainer();
        $this->context->currency = new Currency(1);
        $this->context->language = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $this->context->shop = new Shop(1);
        $this->context->customer = new Customer(self::CUSTOMER_ID);
        $this->context->cart = $this->createCart(self::CUSTOMER_ID, self::ADDRESS_ID);

        $this->originalDb = Db::getInstance();
        $this->db = new class(_DB_SERVER_, _DB_USER_, _DB_PASSWD_, _DB_NAME_) extends DbPDO {
            /** @var array<int, int[]> */
            public array $queriedRuleIds = [];

            public function executeS($sql, $array = true, $use_cache = true)
            {
                $result = parent::executeS($sql, $array, $use_cache);
                if (is_string($sql) && str_contains($sql, 'SELECT SQL_NO_CACHE cr.*')) {
                    $this->queriedRuleIds[] = array_map('intval', array_column($result ?: [], 'id_cart_rule'));
                }

                return $result;
            }
        };
    }

    protected function tearDown(): void
    {
        Db::setInstanceForTesting($this->originalDb);
        DatabaseDump::restoreTables([
            'cart', 'cart_cart_rule', 'cart_product', 'cart_rule', 'cart_rule_carrier', 'cart_rule_country', 'cart_rule_group',
            'cart_rule_lang', 'cart_rule_shop', 'configuration',
        ]);
        Configuration::resetStaticCache();
        CartRule::resetStaticCache();
        Cache::clean('*');
        parent::tearDown();
    }

    public function testARuleRestrictedToSeveralCountriesIsAddedOnce(): void
    {
        $ruleId = $this->createRule('country_restriction');
        Db::getInstance()->execute(
            'INSERT INTO ' . _DB_PREFIX_ . 'cart_rule_country (id_cart_rule, id_country)
             SELECT ' . $ruleId . ', id_country FROM ' . _DB_PREFIX_ . 'country ORDER BY id_country LIMIT 50'
        );

        $this->autoAddToCart();

        $this->assertSame([[$ruleId]], $this->db->queriedRuleIds, 'the rule came back once per country');
        $this->assertSame([$ruleId], $this->getCartRuleIds());
    }

    public function testACarrierRestrictedRuleFollowsTheCartCarrier(): void
    {
        $matchingRuleId = $this->createRule('carrier_restriction');
        $otherRuleId = $this->createRule('carrier_restriction');
        Db::getInstance()->insert('cart_rule_carrier', [
            ['id_cart_rule' => $matchingRuleId, 'id_carrier' => self::CARRIER_ID],
            ['id_cart_rule' => $otherRuleId, 'id_carrier' => 1],
        ]);

        $this->autoAddToCart();

        $this->assertSame([[$matchingRuleId]], $this->db->queriedRuleIds);
        $this->assertSame([$matchingRuleId], $this->getCartRuleIds());
    }

    public function testAGuestGetsTheRulesOfTheUnidentifiedGroup(): void
    {
        $this->context->customer = new Customer();
        $this->context->cart = $this->createCart(0, 0);
        $unidentifiedRuleId = $this->createRule('group_restriction');
        $customerRuleId = $this->createRule('group_restriction');
        Db::getInstance()->insert('cart_rule_group', [
            ['id_cart_rule' => $unidentifiedRuleId, 'id_group' => (int) Configuration::get('PS_UNIDENTIFIED_GROUP')],
            ['id_cart_rule' => $customerRuleId, 'id_group' => (int) Configuration::get('PS_CUSTOMER_GROUP')],
        ]);

        $this->autoAddToCart();

        $this->assertSame([[$unidentifiedRuleId]], $this->db->queriedRuleIds);
        $this->assertSame([$unidentifiedRuleId], $this->getCartRuleIds());
    }

    private function autoAddToCart(): void
    {
        Db::setInstanceForTesting($this->db);
        try {
            CartRule::autoAddToCart($this->context);
        } finally {
            Db::setInstanceForTesting($this->originalDb);
        }
    }

    private function createCart(int $customerId, int $addressId): Cart
    {
        $cart = new Cart();
        $cart->id_customer = $customerId;
        $cart->id_address_delivery = $addressId;
        $cart->id_address_invoice = $addressId;
        $cart->id_currency = 1;
        $cart->id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart->id_shop = 1;
        $cart->id_carrier = self::CARRIER_ID;
        $cart->add();
        $cart->updateQty(1, 1);

        return $cart;
    }

    private function createRule(string $restriction): int
    {
        $rule = new CartRule();
        $rule->name = [(int) Configuration::get('PS_LANG_DEFAULT') => 'Automatic rule'];
        $rule->date_from = date('Y-m-d H:i:s', strtotime('-1 day'));
        $rule->date_to = date('Y-m-d H:i:s', strtotime('+30 days'));
        $rule->quantity = 10;
        $rule->quantity_per_user = 10;
        $rule->reduction_percent = 10;
        $rule->code = '';
        $rule->active = true;
        $rule->{$restriction} = true;
        $rule->add();

        return (int) $rule->id;
    }

    /**
     * @return int[]
     */
    private function getCartRuleIds(): array
    {
        return array_map('intval', array_column(
            Db::getInstance()->executeS('SELECT id_cart_rule FROM ' . _DB_PREFIX_ . 'cart_cart_rule WHERE id_cart = ' . (int) $this->context->cart->id . ' ORDER BY id_cart_rule'),
            'id_cart_rule'
        ));
    }
}
