<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\ProductResetter;

class ProductImporterFeaturesTest extends AbstractProductImportEngineTestCase
{
    private const FIELDS = ['name', 'reference', 'features'];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::resetImportTables();
    }

    public static function tearDownAfterClass(): void
    {
        self::resetImportTables();
        parent::tearDownAfterClass();
    }

    private static function resetImportTables(): void
    {
        ProductResetter::resetProducts();
        DatabaseDump::restoreTables(['feature', 'feature_lang', 'feature_shop', 'feature_value', 'feature_value_lang']);
    }

    public function testFeaturesAreResolvedAutoCreatedAndAssociated(): void
    {
        [, $messages] = $this->runImport('product_features.csv', self::FIELDS);
        $this->assertNoErrors($messages);

        $firstProductId = $this->getProductIdByReference('FEAT-1');
        $secondProductId = $this->getProductIdByReference('FEAT-2');
        $this->assertNotNull($firstProductId);
        $this->assertNotNull($secondProductId);

        // existing feature reused, new one auto-created exactly once (cache)
        $compositionId = $this->fetchOne("SELECT fl.id_feature FROM {p}feature_lang fl WHERE fl.name = 'Composition' AND fl.id_lang = 1");
        $this->assertNotFalse($compositionId);
        $importedFeatureIds = $this->fetchAll("SELECT DISTINCT fl.id_feature FROM {p}feature_lang fl WHERE fl.name = 'Imported Feature' AND fl.id_lang = 1");
        $this->assertCount(1, $importedFeatureIds, 'The new feature must be created exactly once');
        $importedFeatureId = (int) $importedFeatureIds[0]['id_feature'];

        // predefined value auto-created once and shared between both rows
        $importedValueIds = $this->fetchAll("SELECT DISTINCT fv.id_feature_value FROM {p}feature_value fv INNER JOIN {p}feature_value_lang fvl ON fvl.id_feature_value = fv.id_feature_value WHERE fv.id_feature = :feature AND fvl.value = 'Imported Value' AND fvl.id_lang = 1 AND fv.custom = 0", ['feature' => $importedFeatureId]);
        $this->assertCount(1, $importedValueIds, 'The new feature value must be created exactly once');
        $importedValueId = (int) $importedValueIds[0]['id_feature_value'];

        // 'Cotton' is a stock predefined value of Composition: reused, not duplicated
        $cottonIds = $this->fetchAll("SELECT DISTINCT fv.id_feature_value FROM {p}feature_value fv INNER JOIN {p}feature_value_lang fvl ON fvl.id_feature_value = fv.id_feature_value WHERE fv.id_feature = :feature AND fvl.value = 'Cotton' AND fvl.id_lang = 1 AND fv.custom = 0", ['feature' => $compositionId]);
        $this->assertCount(1, $cottonIds);

        // associations
        $firstProductFeatures = $this->fetchAll('SELECT id_feature, id_feature_value FROM {p}feature_product WHERE id_product = :id', ['id' => $firstProductId]);
        $this->assertCount(2, $firstProductFeatures);

        $secondProductFeatures = $this->fetchAll('SELECT id_feature, id_feature_value FROM {p}feature_product WHERE id_product = :id ORDER BY id_feature_value', ['id' => $secondProductId]);
        $this->assertCount(2, $secondProductFeatures);
        $this->assertContains((string) $importedValueId, array_map('strval', array_column($secondProductFeatures, 'id_feature_value')));

        // the custom entry produced a custom feature value
        $customValueIds = array_values(array_diff(
            array_map('strval', array_column($secondProductFeatures, 'id_feature_value')),
            [(string) $importedValueId]
        ));
        $this->assertCount(1, $customValueIds);
        $this->assertSame('1', (string) $this->fetchOne('SELECT custom FROM {p}feature_value WHERE id_feature_value = :id', ['id' => $customValueIds[0]]));
        $this->assertSame('Custom note', (string) $this->fetchOne('SELECT value FROM {p}feature_value_lang WHERE id_feature_value = :id AND id_lang = 1', ['id' => $customValueIds[0]]));
    }
}
