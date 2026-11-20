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
    private const PRODUCT_IN_BOTH = 1;
    private const PRODUCT_IN_A_ONLY = 2;

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
     * @dataProvider getConditionTypes
     */
    public function testGetAffectedProductsHandlesMoreThan61Conditions(string $type): void
    {
        $conditions = [];
        for ($i = 1; $i <= 62; ++$i) {
            $conditions[] = ['type' => $type, 'value' => 900100 + $i];
        }
        $rule = $this->createRule($conditions);

        $this->assertIsArray($rule->getAffectedProducts());
    }

    public static function getConditionTypes(): iterable
    {
        yield 'category' => ['category'];
        yield 'feature' => ['feature'];
        yield 'attribute' => ['attribute'];
    }

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

    public function testGetAffectedProductsRequiresCombinationToHaveEveryAttributeOfTheGroup(): void
    {
        // Fixture combinations of product 1: 1 = attributes 1 and 8, 2 = 1 and 11, 3 = 2 and 8
        $rule = $this->createRule([
            ['type' => 'attribute', 'value' => 1],
            ['type' => 'attribute', 'value' => 8],
        ]);

        $combinationIds = array_map('intval', array_column($rule->getAffectedProducts(), 'id_product_attribute'));

        $this->assertSame([1], $combinationIds);
    }

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
