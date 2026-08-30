<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use Tests\Resources\Resetter\ProductResetter;

/**
 * Review-driven behavior fixes: numeric ids never become entity names, invalid
 * numeric cells are warned and ignored instead of silently cast, the shop cell
 * honors the configured multiple-value separator and existence-checks ids.
 */
class ProductImporterBehaviorFixesTest extends AbstractProductImportEngineTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        ProductResetter::resetProducts();
    }

    public static function tearDownAfterClass(): void
    {
        ProductResetter::resetProducts();
        parent::tearDownAfterClass();
    }

    public function testInvalidNumericFieldsAreWarnedAndIgnored(): void
    {
        [, $messages] = $this->runImport(
            'product_behavior_fixes.csv',
            ['name', 'reference', 'manufacturer', 'minimal_quantity', 'out_of_stock', 'low_stock_alert']
        );
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('FIX-1');
        $this->assertNotNull($productId);

        // a numeric manufacturer cell is an ID, never a name: unknown id 999999
        // must be warned and dropped, not become a brand named "999999"
        $this->assertNotEmpty($this->warningsForField($messages, 'manufacturer'));
        $this->assertSame(0, (int) $this->fetchOne("SELECT COUNT(*) FROM {p}manufacturer WHERE name = '999999'"), 'A brand must never be created from a numeric id');
        $this->assertSame(0, (int) $this->fetchOne('SELECT id_manufacturer FROM {p}product WHERE id_product = :id', ['id' => $productId]));

        // minimal_quantity is a count: a negative value is warned and ignored
        $this->assertNotEmpty($this->warningsForField($messages, 'minimal_quantity'));
        $this->assertSame(1, (int) $this->fetchOne('SELECT minimal_quantity FROM {p}product WHERE id_product = :id', ['id' => $productId]));

        // a non-integer out_of_stock is warned and ignored — the legacy (int)
        // cast silently turned "abc" into the valid enum value 0
        $this->assertNotEmpty($this->warningsForField($messages, 'out_of_stock'));
        $this->assertSame(2, (int) $this->fetchOne('SELECT out_of_stock FROM {p}stock_available WHERE id_product = :id AND id_product_attribute = 0', ['id' => $productId]), 'out_of_stock must keep its default value');

        // low_stock_alert without a usable low_stock_threshold gets its own warning
        $alertWarnings = $this->warningsForField($messages, 'low_stock_alert');
        $this->assertCount(1, $alertWarnings);
        $this->assertStringContainsString('low_stock_threshold', $alertWarnings[0]->message);
    }

    public function testShopEntriesHonorTheSeparatorAndUnknownIdsAreDropped(): void
    {
        // '|' as multiple-value separator + one valid id, one unknown id, one unknown name
        $context = $this->buildContext('product_shops.csv', ['name', 'reference', 'shop'], [], 1, ';', '|');
        $messages = (new ImportEngineTestRunner())->run($this->getEntityImporter(), $context);
        $this->assertNoErrors($messages);

        $productId = $this->getProductIdByReference('SHP-1');
        $this->assertNotNull($productId);
        $this->assertNotFalse($this->fetchOne('SELECT 1 FROM {p}product_shop WHERE id_product = :id AND id_shop = 1', ['id' => $productId]));

        $shopWarnings = $this->warningsForField($messages, 'shop');
        $this->assertCount(2, $shopWarnings);
        $this->assertStringContainsString('"99"', $shopWarnings[0]->message, 'An unknown numeric shop id must be warned and dropped, exactly like an unknown name');
        $this->assertStringContainsString('"Nonexistent Shop"', $shopWarnings[1]->message);
    }

    /**
     * @param list<ImportMessage> $messages
     *
     * @return list<ImportMessage>
     */
    private function warningsForField(array $messages, string $field): array
    {
        return array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => $field === $message->field
        ));
    }
}
