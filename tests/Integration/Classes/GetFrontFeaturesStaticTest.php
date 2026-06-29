<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Configuration;
use Db;
use Feature;
use FeatureValue;
use Language;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagManager;
use PrestaShop\PrestaShop\Core\FeatureFlag\FeatureFlagSettings;
use Product;
use Shop;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMockerTrait;
use Tests\Resources\Resetter\FeatureFlagResetter;

/**
 * Covers the front-office feature retrieval that merges product-level and combination-level
 * feature values. The merge is gated by the "combination_feature_values" feature flag: when the
 * flag is off Product::getFrontFeaturesStatic() keeps its historical product-only behavior, and
 * when it is on the combination values take precedence over the product ones for a shared feature.
 */
class GetFrontFeaturesStaticTest extends KernelTestCase
{
    use ContextMockerTrait;

    private const ID_PRODUCT = 999001;
    private const ID_PRODUCT_ATTRIBUTE = 999001;

    /**
     * @var int
     */
    private $langId;

    /**
     * @var int
     */
    private $shopId;

    /**
     * @var int
     */
    private $featureId;

    /**
     * @var FeatureFlagManager
     */
    private $featureFlagManager;

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        FeatureFlagResetter::resetFeatureFlags();
    }

    protected function setUp(): void
    {
        parent::setUp();

        self::mockContext();

        // Init Symfony so the container is reachable: Product::getFrontFeaturesStatic resolves the
        // feature flag checker through ContainerFinder.
        self::bootKernel();
        // Global var read by SymfonyContainer::getInstance() / ContainerFinder.
        global $kernel;
        $kernel = self::$kernel;

        $this->featureFlagManager = self::getContainer()->get(FeatureFlagManager::class);

        $this->langId = (int) Configuration::get('PS_LANG_DEFAULT');
        $this->shopId = (int) Shop::getContextShopID() ?: 1;
        Configuration::updateValue('PS_FEATURE_FEATURE_ACTIVE', 1);

        $this->createFixtures();
    }

    protected function tearDown(): void
    {
        $this->cleanFixtures();
        $this->setCombinationFeatureValuesEnabled(false);
        Product::resetStaticCache();
        parent::tearDown();
    }

    public function testProductFeaturesAreReturnedWhenNoCombinationIsTargeted(): void
    {
        $this->setCombinationFeatureValuesEnabled(true);

        $features = Product::getFrontFeaturesStatic($this->langId, self::ID_PRODUCT);

        $this->assertSame(['Product value'], array_column($features, 'value'));
    }

    public function testCombinationFeaturesAreIgnoredWhenFlagIsDisabled(): void
    {
        $this->setCombinationFeatureValuesEnabled(false);

        $features = Product::getFrontFeaturesStatic($this->langId, self::ID_PRODUCT, self::ID_PRODUCT_ATTRIBUTE);

        // Flag off: the combination is ignored, only the product value is returned.
        $this->assertSame(['Product value'], array_column($features, 'value'));
    }

    public function testCombinationFeaturesOverrideProductFeaturesWhenFlagIsEnabled(): void
    {
        $this->setCombinationFeatureValuesEnabled(true);

        $features = Product::getFrontFeaturesStatic($this->langId, self::ID_PRODUCT, self::ID_PRODUCT_ATTRIBUTE);

        // Flag on + same feature at both levels: the combination value wins.
        $this->assertSame(['Combination value'], array_column($features, 'value'));
    }

    private function createFixtures(): void
    {
        $langIds = Language::getIDs(false);

        $feature = new Feature();
        $feature->name = array_fill_keys($langIds, 'Test feature');
        $feature->add();
        $this->featureId = (int) $feature->id;

        Db::getInstance()->insert('feature_shop', [
            'id_feature' => $this->featureId,
            'id_shop' => $this->shopId,
        ], false, true, Db::INSERT_IGNORE);

        $productFeatureValueId = $this->createFeatureValue('Product value', $langIds);
        $combinationFeatureValueId = $this->createFeatureValue('Combination value', $langIds);

        Db::getInstance()->insert('feature_product', [
            'id_feature' => $this->featureId,
            'id_product' => self::ID_PRODUCT,
            'id_feature_value' => $productFeatureValueId,
        ]);
        Db::getInstance()->insert('feature_product_attribute', [
            'id_feature' => $this->featureId,
            'id_product_attribute' => self::ID_PRODUCT_ATTRIBUTE,
            'id_feature_value' => $combinationFeatureValueId,
        ]);
    }

    /**
     * @param int[] $langIds
     */
    private function createFeatureValue(string $value, array $langIds): int
    {
        $featureValue = new FeatureValue();
        $featureValue->id_feature = $this->featureId;
        $featureValue->custom = false;
        $featureValue->value = array_fill_keys($langIds, $value);
        $featureValue->add();

        return (int) $featureValue->id;
    }

    private function cleanFixtures(): void
    {
        if (empty($this->featureId)) {
            return;
        }

        Db::getInstance()->delete('feature_product', 'id_product = ' . self::ID_PRODUCT);
        Db::getInstance()->delete('feature_product_attribute', 'id_product_attribute = ' . self::ID_PRODUCT_ATTRIBUTE);
        Db::getInstance()->delete('feature_shop', 'id_feature = ' . $this->featureId);

        $feature = new Feature($this->featureId);
        $feature->delete();
    }

    private function setCombinationFeatureValuesEnabled(bool $enabled): void
    {
        if ($enabled) {
            $this->featureFlagManager->enable(FeatureFlagSettings::FEATURE_FLAG_COMBINATION_FEATURE_VALUES);
        } else {
            $this->featureFlagManager->disable(FeatureFlagSettings::FEATURE_FLAG_COMBINATION_FEATURE_VALUES);
        }
        // Drop the per-request state cache so the new DB value is read back, and the product caches
        // so the merge is recomputed.
        $this->featureFlagManager->reset();
        Product::resetStaticCache();
    }
}
