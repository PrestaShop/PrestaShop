<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\ProductResetter;

/**
 * An import handed nothing but product rows can still add brands, categories,
 * features and feature values (legacy behavior, kept), so it says so: every
 * auto-creation is reported as a NOTICE.
 *
 * Notice rather than warning because auto-creation is expected, not a problem —
 * and a pause could not help anyway, since the entity exists by the time we know
 * and the database phase never pauses. The quiet resolver caches are what keep
 * this to one message per created entity instead of one per row.
 */
class ProductImporterAutoCreationTest extends AbstractProductImportEngineTestCase
{
    private const FIELDS = ['name', 'reference', 'manufacturer', 'category', 'features'];

    /**
     * The tables are restored per TEST, not per class: both cases below depend
     * on whether the entities already exist, so sharing state between them would
     * make them order-dependent.
     */
    protected function setUp(): void
    {
        parent::setUp();
        self::restoreCatalogEntities();
    }

    public static function tearDownAfterClass(): void
    {
        self::restoreCatalogEntities();
        parent::tearDownAfterClass();
    }

    private static function restoreCatalogEntities(): void
    {
        ProductResetter::resetProducts();
        DatabaseDump::restoreTables([
            'manufacturer', 'manufacturer_shop', 'manufacturer_lang',
            'category', 'category_shop', 'category_lang',
            'feature', 'feature_shop', 'feature_lang', 'feature_value', 'feature_value_lang',
        ]);
    }

    public function testEveryAutoCreatedEntityIsReportedOnceWithItsName(): void
    {
        // two rows naming the SAME new brand, category path and feature, so the
        // once-per-batch behaviour is exercised by the same import
        [, $messages] = $this->runImport('product_auto_creation.csv', self::FIELDS, [], null, 5);
        $this->assertNoErrors($messages);

        $notices = $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_NOTICE);
        foreach ($notices as $notice) {
            $this->assertSame(ImportPhaseDefinition::PHASE_DATABASE, $notice->phase, 'Auto-creation is reported from the phase that performs it');
        }

        $texts = array_map(static fn (ImportMessage $message): string => $message->message, $notices);

        // one per created entity: the brand, BOTH path segments, the feature and
        // its value — and each exactly once despite two rows naming them
        $expected = [
            'Brand "Brand To Create" did not exist and was created.',
            'Category "Parent To Create" did not exist and was created.',
            'Category "Child To Create" did not exist and was created.',
            'Feature "Feature To Create" did not exist and was created.',
            'Feature value "Value To Create" did not exist and was created.',
        ];
        foreach ($expected as $message) {
            $this->assertSame(
                1,
                count(array_keys($texts, $message, true)),
                sprintf('Expected exactly one "%s". Got: %s', $message, implode(' | ', $texts))
            );
        }
        $this->assertCount(count($expected), $notices, 'No auto-creation notice beyond the five created entities');

        // and the entities really were created, so the notices are not cosmetic
        $this->assertNotFalse($this->fetchOne('SELECT id_manufacturer FROM {p}manufacturer WHERE name = :name', ['name' => 'Brand To Create']));
        $childCategoryId = $this->fetchOne('SELECT id_category FROM {p}category_lang WHERE name = :name AND id_lang = 1', ['name' => 'Child To Create']);
        $this->assertNotFalse($childCategoryId);
        $parentCategoryId = $this->fetchOne('SELECT id_category FROM {p}category_lang WHERE name = :name AND id_lang = 1', ['name' => 'Parent To Create']);
        $this->assertSame(
            (int) $parentCategoryId,
            (int) $this->fetchOne('SELECT id_parent FROM {p}category WHERE id_category = :id', ['id' => $childCategoryId]),
            'The second path segment must have been created UNDER the first'
        );
        $this->assertNotFalse($this->fetchOne('SELECT id_feature FROM {p}feature_lang WHERE name = :name AND id_lang = 1', ['name' => 'Feature To Create']));
        $this->assertNotFalse($this->fetchOne('SELECT id_feature_value FROM {p}feature_value_lang WHERE value = :value AND id_lang = 1', ['value' => 'Value To Create']));
    }

    /**
     * Reusing entities that already exist must stay silent — the notice marks a
     * CHANGE to the catalog, not the mere fact that a row referenced a brand.
     */
    public function testReusingExistingEntitiesReportsNothing(): void
    {
        // first pass creates the brand, the two categories, the feature and its value
        $this->runImport('product_auto_creation.csv', self::FIELDS, [], null, 5);

        // start the second pass from a clean product set: the rows must create
        // products again rather than collide on their own references, while the
        // catalog entities they name are all in place by now
        ProductResetter::resetProducts();

        [, $messages] = $this->runImport('product_auto_creation.csv', self::FIELDS, [], null, 5);
        $this->assertNoErrors($messages);

        $this->assertSame([], $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_NOTICE), 'Nothing was created, so nothing to report');
    }
}
