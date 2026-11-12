<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use PHPUnit\Framework\TestCase;
use Product;
use SpecificPriceRule;
use Tests\Resources\DatabaseDump;

class ProductWebserviceFeaturesTest extends TestCase
{
    private const TABLES = [
        'product', 'product_shop', 'product_lang', 'feature', 'feature_lang', 'feature_value', 'feature_value_lang', 'feature_product',
        'specific_price', 'specific_price_rule', 'specific_price_rule_condition', 'specific_price_rule_condition_group',
        'stock_available', 'category_product',
    ];

    /**
     * @var int
     */
    private static $productId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(self::TABLES);

        $product = new Product();
        $product->name = [1 => 'Features perf probe'];
        $product->link_rewrite = [1 => 'features-perf-probe'];
        $product->price = 10.0;
        $product->add();
        self::$productId = (int) $product->id;
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        DatabaseDump::restoreTables(self::TABLES);
    }

    public function testTheCostDoesNotGrowWithAFullRecalculationPerFeature(): void
    {
        $smallSet = $this->countQueriesForFeatures(2);
        $largeSet = $this->countQueriesForFeatures(20);

        $extraFeatures = 18;
        $this->assertLessThanOrEqual(
            $extraFeatures + 10,
            $largeSet - $smallSet,
            sprintf(
                'Adding %d more features cost %d extra queries (2 features: %d, 20 features: %d), which means the price rules are still recalculated per feature.',
                $extraFeatures,
                $largeSet - $smallSet,
                $smallSet,
                $largeSet
            )
        );
    }

    public function testAFeatureConditionedPriceRuleFollowsTheFeatures(): void
    {
        $rule = new SpecificPriceRule();
        $rule->name = 'Feature rule probe';
        $rule->id_shop = 1;
        $rule->id_currency = 0;
        $rule->id_country = 0;
        $rule->id_group = 0;
        $rule->from_quantity = 1;
        $rule->price = -1;
        $rule->reduction = 0.1;
        $rule->reduction_tax = true;
        $rule->reduction_type = 'percentage';
        $rule->from = '0000-00-00 00:00:00';
        $rule->to = '0000-00-00 00:00:00';
        $rule->add();
        $rule->addConditions([['type' => 'feature', 'value' => 1]]);

        $product = new Product(self::$productId);
        $product->setWsProductFeatures([['id' => 1, 'id_feature_value' => 1]]);
        $this->assertSame(1, $this->countRulePrices((int) $rule->id));

        $product->setWsProductFeatures([]);
        $this->assertSame(0, $this->countRulePrices((int) $rule->id));
    }

    private function countRulePrices(int $ruleId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM ' . _DB_PREFIX_ . 'specific_price WHERE id_product = ' . self::$productId . ' AND id_specific_price_rule = ' . $ruleId
        );
    }

    private function countQueriesForFeatures(int $featureCount): int
    {
        $product = new Product(self::$productId);
        $features = [];
        for ($i = 1; $i <= $featureCount; ++$i) {
            $features[] = ['id' => $i, 'id_feature_value' => $i];
        }

        $before = $this->getExecutedStatements();
        $product->setWsProductFeatures($features);

        return $this->getExecutedStatements() - $before;
    }

    private function getExecutedStatements(): int
    {
        $status = Db::getInstance()->executeS("SHOW SESSION STATUS LIKE 'Questions'");

        return (int) $status[0]['Value'];
    }
}
