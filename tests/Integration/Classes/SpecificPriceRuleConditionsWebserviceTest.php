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

class SpecificPriceRuleConditionsWebserviceTest extends KernelTestCase
{
    private const CAT_A = 900001;
    private const CAT_B = 900002;

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
            'specific_price',
            'specific_price_rule',
            'specific_price_rule_condition',
            'specific_price_rule_condition_group',
        ]);
    }

    protected function setUp(): void
    {
        self::bootKernel();
        Context::getContext()->container = self::getContainer();
    }

    public function testGetterFlattensTheConditionGroupsIntoRowsCarryingTheirGroup(): void
    {
        $rule = $this->createRule();
        $rule->addConditions([
            ['type' => 'category', 'value' => self::CAT_A],
            ['type' => 'category', 'value' => self::CAT_B],
        ]);
        $rule->addConditions([['type' => 'manufacturer', 'value' => 5]]);

        $rows = $rule->getWsSpecificPriceRuleConditions();

        $this->assertCount(3, $rows);
        $groups = array_unique(array_column($rows, 'id_specific_price_rule_condition_group'));
        $this->assertCount(2, $groups, 'the two groups must stay distinct');

        $byGroup = [];
        foreach ($rows as $row) {
            $byGroup[$row['id_specific_price_rule_condition_group']][] = $row['type'] . ':' . $row['value'];
        }
        sort($byGroup);
        $this->assertSame(
            [['manufacturer:5'], ['category:' . self::CAT_A, 'category:' . self::CAT_B]],
            array_values($byGroup)
        );
    }

    public function testSetterRebuildsTheGroupsFromTheGroupKeyOfEachRow(): void
    {
        $rule = $this->createRule();

        $this->assertTrue($rule->setWsSpecificPriceRuleConditions([
            ['id_specific_price_rule_condition_group' => '1', 'type' => 'category', 'value' => (string) self::CAT_A],
            ['id_specific_price_rule_condition_group' => '1', 'type' => 'category', 'value' => (string) self::CAT_B],
            ['id_specific_price_rule_condition_group' => '2', 'type' => 'manufacturer', 'value' => '5'],
        ]));

        $stored = $rule->getConditions();
        $this->assertCount(2, $stored);
        $sizes = array_map('count', array_values($stored));
        sort($sizes);
        $this->assertSame([1, 2], $sizes);
    }

    public function testSetterKeepsTheExistingConditionsWhenThePayloadIsRejected(): void
    {
        $rule = $this->createRule();
        $rule->addConditions([['type' => 'category', 'value' => self::CAT_A]]);

        $this->assertFalse($rule->setWsSpecificPriceRuleConditions([
            ['id_specific_price_rule_condition_group' => '1', 'type' => 'shipping_cost', 'value' => '5'],
        ]));

        // A rejected payload that had already dropped the conditions would leave the rule
        // with none, which applies it to the whole catalog.
        $this->assertCount(1, $rule->getWsSpecificPriceRuleConditions());
    }

    public function testAddConditionsRefusesToCreateAGroupWithoutConditions(): void
    {
        $rule = $this->createRule();

        $this->assertFalse($rule->addConditions([]));
        $this->assertFalse($rule->addConditions([['type' => 'category', 'value' => 0]]));
        $this->assertSame(
            '0',
            (string) Db::getInstance()->getValue(
                'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'specific_price_rule_condition_group
                 WHERE id_specific_price_rule = ' . (int) $rule->id
            )
        );
    }

    private function createRule(): SpecificPriceRule
    {
        $rule = new SpecificPriceRule();
        $rule->name = 'test-rule-27396';
        $rule->id_shop = (int) Context::getContext()->shop->id;
        $rule->id_country = 0;
        $rule->id_currency = 0;
        $rule->id_group = 0;
        $rule->from_quantity = 1;
        $rule->price = -1;
        $rule->reduction = 0;
        $rule->reduction_tax = 1;
        $rule->reduction_type = 'amount';
        $rule->from = '2020-01-01 00:00:00';
        $rule->to = '2030-01-01 00:00:00';
        $rule->add();

        return $rule;
    }
}
