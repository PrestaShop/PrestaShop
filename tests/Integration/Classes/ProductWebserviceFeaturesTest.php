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
use Tests\Resources\DatabaseDump;

class ProductWebserviceFeaturesTest extends TestCase
{
    /**
     * @var int
     */
    private static $productId;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        DatabaseDump::restoreTables(['product', 'product_shop', 'product_lang', 'feature', 'feature_lang', 'feature_value', 'feature_value_lang', 'feature_product', 'specific_price']);

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
        DatabaseDump::restoreTables(['product', 'product_shop', 'product_lang', 'feature', 'feature_lang', 'feature_value', 'feature_value_lang', 'feature_product', 'specific_price']);
    }

    /**
     * setWsProductFeatures() used to call SpecificPriceRule::applyAllRules() from addFeaturesToDB(),
     * so one webservice call carrying N features paid for N full price recalculations. The cost of
     * the call must depend on the number of features only through the inserts, not through a
     * recalculation per value, so the difference between a small and a large set has to stay close
     * to the number of extra inserts.
     */
    public function testTheCostDoesNotGrowWithAFullRecalculationPerFeature(): void
    {
        $smallSet = $this->countQueriesForFeatures(2);
        $largeSet = $this->countQueriesForFeatures(20);

        $extraFeatures = 18;
        // One insert per extra feature, plus a small constant margin for the surrounding statements.
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
