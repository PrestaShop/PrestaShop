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
use Tests\Resources\Resetter\LanguageResetter;
use Tests\Resources\Resetter\ProductResetter;
use Tests\Resources\ResourceResetter;

class ProductImporterMultilangTest extends AbstractProductImportEngineTestCase
{
    use LanguageTrait;

    private const FIELDS = ['name', 'reference', 'description'];

    private static int $frenchLanguageId;

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
        LanguageResetter::resetLanguages();
        parent::tearDownAfterClass();
    }

    public function testCreationDuplicatesIntoEveryLanguageAndUpdateOnlyWritesTheFileLanguage(): void
    {
        self::bootKernel();
        self::$frenchLanguageId = self::addLanguageByLocale('fr-FR');
        // ObjectModel multilang writes iterate the static language list, which
        // may have been cached before the language was created
        Language::resetStaticCache();
        Cache::clean('*');
        ProductResetter::resetProducts();

        // creation: single-language file duplicated into every language
        [, $messages] = $this->runImport('product_multilang_create.csv', self::FIELDS);
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('ML-1');
        $this->assertNotNull($productId);
        $this->assertSame('Multilang Product', $this->getLocalizedName($productId, 1));
        $this->assertSame('Multilang Product', $this->getLocalizedName($productId, self::$frenchLanguageId));

        // update (match_ref): only the file's language (en) is written
        [, $messages] = $this->runImport('product_multilang_update.csv', self::FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $this->assertSame('Multilang Product Updated', $this->getLocalizedName($productId, 1));
        $this->assertSame('Multilang Product', $this->getLocalizedName($productId, self::$frenchLanguageId), 'An update must not touch the other languages');
    }

    private function getLocalizedName(int $productId, int $languageId): string
    {
        return (string) $this->fetchOne(
            'SELECT name FROM {p}product_lang WHERE id_product = :id AND id_lang = :lang AND id_shop = 1',
            ['id' => $productId, 'lang' => $languageId]
        );
    }
}
