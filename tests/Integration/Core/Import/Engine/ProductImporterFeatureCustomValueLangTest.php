<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use Cache;
use Language;
use Tests\Integration\Utility\LanguageTrait;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\LanguageResetter;
use Tests\Resources\Resetter\ProductResetter;
use Tests\Resources\ResourceResetter;

/**
 * Custom feature values follow the same single-language-file rule as every
 * other localized field: duplicated into every language on creation, only the
 * file's language written on update.
 */
class ProductImporterFeatureCustomValueLangTest extends AbstractProductImportEngineTestCase
{
    use LanguageTrait;

    private const FIELDS = ['name', 'reference', 'features'];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        // LanguageResetter ends with resetTestModules(), which mirrors the
        // test modules from a temp backup: refresh that backup now so the
        // teardown cannot restore a stale tree
        (new ResourceResetter())->backupTestModules();
    }

    public static function tearDownAfterClass(): void
    {
        ProductResetter::resetProducts();
        DatabaseDump::restoreTables(['feature', 'feature_lang', 'feature_shop', 'feature_value', 'feature_value_lang']);
        LanguageResetter::resetLanguages();
        parent::tearDownAfterClass();
    }

    public function testCustomValueIsDuplicatedOnCreationAndSingleLanguageOnUpdate(): void
    {
        self::bootKernel();
        $frenchLanguageId = self::addLanguageByLocale('fr-FR');
        Language::resetStaticCache();
        Cache::clean('*');
        ProductResetter::resetProducts();

        // creation: the custom value is duplicated into every language
        [, $messages] = $this->runImport('product_features_custom_create.csv', self::FIELDS);
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('MLF-1');
        $this->assertNotNull($productId);
        $customValueId = $this->getCustomValueId($productId);
        $this->assertSame('First note', $this->getCustomValue($customValueId, 1));
        $this->assertSame('First note', $this->getCustomValue($customValueId, $frenchLanguageId));

        // update (match_ref): only the file's language (en) receives the new text
        [, $messages] = $this->runImport('product_features_custom_update.csv', self::FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $updatedValueId = $this->getCustomValueId($productId);
        $this->assertSame('Updated note', $this->getCustomValue($updatedValueId, 1));
        $this->assertNotSame('Updated note', $this->getCustomValue($updatedValueId, $frenchLanguageId), 'An update from a single-language file must not write the other languages');
    }

    private function getCustomValueId(int $productId): int
    {
        $customValueId = $this->fetchOne(
            'SELECT fv.id_feature_value FROM {p}feature_product fp INNER JOIN {p}feature_value fv ON fv.id_feature_value = fp.id_feature_value WHERE fp.id_product = :id AND fv.custom = 1',
            ['id' => $productId]
        );
        $this->assertNotFalse($customValueId, 'The row must carry exactly one custom feature value');

        return (int) $customValueId;
    }

    private function getCustomValue(int $featureValueId, int $languageId): string
    {
        return (string) $this->fetchOne(
            'SELECT value FROM {p}feature_value_lang WHERE id_feature_value = :id AND id_lang = :lang',
            ['id' => $featureValueId, 'lang' => $languageId]
        );
    }
}
