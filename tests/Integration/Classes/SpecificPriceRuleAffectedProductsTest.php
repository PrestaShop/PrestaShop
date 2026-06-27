<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Integration\Classes;

use Context;
use Db;
use SpecificPriceRule;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Resources\DatabaseDump;

class SpecificPriceRuleAffectedProductsTest extends KernelTestCase
{
    private const CAT_A = 900001;
    private const CAT_B = 900002;
    private const PRODUCT_IN_BOTH = 20;
    private const PRODUCT_IN_A_ONLY = 21;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::restore();
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        self::restore();
    }

    private static function restore(): void
    {
        DatabaseDump::restoreTables([
            'specific_price_rule',
            'specific_price_rule_condition',
            'specific_price_rule_condition_group',
            'category_product',
        ]);
    }

    protected function setUp(): void
    {
        self::bootKernel();
        Context::getContext()->container = self::getContainer();
    }

    /**
     * A catalog price rule whose group holds more than 61 category conditions used to
     * generate one JOIN per category, hitting MySQL/MariaDB's 61-table join limit
     * (error 1116) and aborting the product save. It must now run without error.
     */
    public function testGetAffectedProductsHandlesMoreThan61CategoryConditions(): void
    {
        $conditions = [];
        for ($i = 1; $i <= 62; ++$i) {
            $conditions[] = ['type' => 'category', 'value' => 900100 + $i];
        }
        $rule = $this->createRule($conditions);

        $this->assertIsArray($rule->getAffectedProducts());
    }

    /**
     * Conditions inside a group are AND-combined: a product must belong to ALL the
     * categories of the group. This pins that semantics so the join-limit fix does not
     * silently turn it into an OR.
     */
    public function testGetAffectedProductsRequiresProductToBelongToEveryCategoryOfTheGroup(): void
    {
        Db::getInstance()->insert('category_product', ['id_category' => self::CAT_A, 'id_product' => self::PRODUCT_IN_BOTH, 'position' => 0]);
        Db::getInstance()->insert('category_product', ['id_category' => self::CAT_B, 'id_product' => self::PRODUCT_IN_BOTH, 'position' => 0]);
        Db::getInstance()->insert('category_product', ['id_category' => self::CAT_A, 'id_product' => self::PRODUCT_IN_A_ONLY, 'position' => 0]);

        $rule = $this->createRule([
            ['type' => 'category', 'value' => self::CAT_A],
            ['type' => 'category', 'value' => self::CAT_B],
        ]);

        $ids = array_map('intval', array_column($rule->getAffectedProducts(), 'id_product'));

        $this->assertContains(self::PRODUCT_IN_BOTH, $ids);
        $this->assertNotContains(self::PRODUCT_IN_A_ONLY, $ids);
    }

    /**
     * Rows are inserted directly (rather than via ObjectModel::add) to keep the fixture
     * independent of unrelated save-time machinery; getAffectedProducts only needs the
     * rule id and the persisted condition groups/conditions.
     */
    private function createRule(array $conditions): SpecificPriceRule
    {
        $shopId = (int) Context::getContext()->shop->id;
        $db = Db::getInstance();

        $db->insert('specific_price_rule', [
            'name' => 'test-rule-40394',
            'id_shop' => $shopId,
            'id_country' => 0,
            'id_currency' => 0,
            'id_group' => 0,
            'from_quantity' => 1,
            'price' => -1,
            'reduction' => 0,
            'reduction_tax' => 1,
            'reduction_type' => 'amount',
        ]);
        $ruleId = (int) $db->Insert_ID();

        $db->insert('specific_price_rule_condition_group', ['id_specific_price_rule' => $ruleId]);
        $groupId = (int) $db->Insert_ID();

        foreach ($conditions as $condition) {
            $db->insert('specific_price_rule_condition', [
                'id_specific_price_rule_condition_group' => $groupId,
                'type' => $condition['type'],
                'value' => (float) $condition['value'],
            ]);
        }

        $rule = new SpecificPriceRule();
        $rule->id = $ruleId;
        $rule->id_shop = $shopId;

        return $rule;
    }
}
