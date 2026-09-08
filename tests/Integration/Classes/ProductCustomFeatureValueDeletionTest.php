<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Classes;

use Db;
use Feature;
use FeatureValue;
use PHPUnit\Framework\TestCase;
use Product;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * Product::deleteFeatures() used to drop a custom feature value from the whole catalogue, so deleting
 * one product emptied the feature on every other product that shared the value and left their
 * feature_product rows pointing at a row that no longer existed.
 *
 * Nothing stops several products sharing one custom value: Product::setWsProductFeatures() inserts
 * whichever id_feature_value the webservice sends, without checking who owns it.
 */
class ProductCustomFeatureValueDeletionTest extends TestCase
{
    use ContextMockerTrait;

    /**
     * @var int
     */
    private $featureId;

    /**
     * @var int[]
     */
    private $createdProductIds = [];

    protected function setUp(): void
    {
        parent::setUp();
        self::mockContext();

        $feature = new Feature();
        $feature->name = [1 => 'Shared custom feature'];
        $feature->add();
        $this->featureId = (int) $feature->id;
    }

    protected function tearDown(): void
    {
        foreach ($this->createdProductIds as $productId) {
            $product = new Product($productId);
            if ($product->id) {
                $product->delete();
            }
        }

        $feature = new Feature($this->featureId);
        if ($feature->id) {
            // Removes the feature and every value still attached to it.
            $feature->delete();
        }

        parent::tearDown();
    }

    public function testDeletingOneProductKeepsACustomValueAnotherProductStillUses(): void
    {
        $customValueId = $this->createCustomFeatureValue('Shared value');
        $firstProductId = $this->createProductWithFeatureValue('first-product', $customValueId);
        $secondProductId = $this->createProductWithFeatureValue('second-product', $customValueId);

        // Guard the fixture itself: without two live associations the test would pass vacuously.
        $this->assertSame(2, $this->countAssociations($customValueId));

        $firstProduct = new Product($firstProductId);
        $this->assertTrue((bool) $firstProduct->delete());

        $this->assertTrue(
            $this->featureValueExists($customValueId),
            'Deleting one product removed a custom feature value that another product still uses.'
        );
        $this->assertSame(
            1,
            $this->countAssociations($customValueId),
            'The surviving product lost its association to the shared custom feature value.'
        );
        $this->assertSame(
            [$secondProductId],
            $this->associatedProductIds($customValueId),
            'The association left behind belongs to the wrong product.'
        );
    }

    /**
     * The guard must not leak rows either: once the last product using the value is gone, the value
     * itself has to go, exactly as it did before.
     */
    public function testDeletingTheLastProductStillRemovesTheCustomValue(): void
    {
        $customValueId = $this->createCustomFeatureValue('Only value');
        $onlyProductId = $this->createProductWithFeatureValue('only-product', $customValueId);

        $this->assertSame(1, $this->countAssociations($customValueId));

        $onlyProduct = new Product($onlyProductId);
        $this->assertTrue((bool) $onlyProduct->delete());

        $this->assertFalse(
            $this->featureValueExists($customValueId),
            'A custom feature value survived the deletion of the only product using it.'
        );
    }

    private function createCustomFeatureValue(string $value): int
    {
        $featureValue = new FeatureValue();
        $featureValue->id_feature = $this->featureId;
        $featureValue->custom = true;
        $featureValue->value = [1 => $value];
        $featureValue->add();

        return (int) $featureValue->id;
    }

    private function createProductWithFeatureValue(string $slug, int $customValueId): int
    {
        $product = new Product(null, false, 1);
        $product->name = [1 => $slug];
        $product->link_rewrite = [1 => $slug];
        $product->price = 10.0;
        $product->add();

        $productId = (int) $product->id;
        $this->createdProductIds[] = $productId;

        // Mirrors what setWsProductFeatures() does: the value id is inserted as given, with no check
        // of whether it already belongs to another product.
        $product->addFeaturesToDB($this->featureId, $customValueId);

        return $productId;
    }

    private function featureValueExists(int $customValueId): bool
    {
        return (bool) Db::getInstance()->getValue(
            'SELECT 1 FROM `' . _DB_PREFIX_ . 'feature_value` WHERE `id_feature_value` = ' . $customValueId
        );
    }

    private function countAssociations(int $customValueId): int
    {
        return (int) Db::getInstance()->getValue(
            'SELECT COUNT(*) FROM `' . _DB_PREFIX_ . 'feature_product` WHERE `id_feature_value` = ' . $customValueId
        );
    }

    /**
     * @return int[]
     */
    private function associatedProductIds(int $customValueId): array
    {
        $rows = Db::getInstance()->executeS(
            'SELECT `id_product` FROM `' . _DB_PREFIX_ . 'feature_product` WHERE `id_feature_value` = ' . $customValueId
        ) ?: [];

        return array_map('intval', array_column($rows, 'id_product'));
    }
}
