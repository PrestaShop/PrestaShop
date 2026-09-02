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
 * Per-field behavior rules reviewed during the refactoring: gtin precedence
 * over its legacy ean13 alias, customization counts (integer, not boolean),
 * and the mutual exclusion of the two reduction kinds.
 */
class ProductImporterFieldRulesTest extends AbstractProductImportEngineTestCase
{
    private const FIELDS = [
        'name', 'reference', 'gtin', 'ean13', 'uploadable_files', 'text_fields', 'customizable',
        'reduction_price', 'reduction_percent', 'price_tex',
    ];

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

    public function testFieldRules(): void
    {
        [, $messages] = $this->runImport('product_field_rules.csv', self::FIELDS);
        $this->assertNoErrors($messages);

        $this->assertGtinWinsOverEan13();
        $this->assertCustomizationCountsCreateRealFields();
        $this->assertBothReductionsAreDropped($messages);

        // explicit 0/0 counts on an update remove every customization field
        [, $messages] = $this->runImport('product_field_rules_customization_clear.csv', ['name', 'reference', 'uploadable_files', 'text_fields'], ['matchRef' => true]);
        $this->assertNoErrors($messages);
        $productId = $this->getProductIdByReference('FLD-CUST-1');
        $this->assertSame(0, (int) $this->fetchOne('SELECT COUNT(*) FROM {p}customization_field WHERE id_product = :id', ['id' => $productId]));
    }

    private function assertGtinWinsOverEan13(): void
    {
        // setGtin() stores into the legacy ean13 column
        $gtinProductId = $this->getProductIdByReference('FLD-GTIN-1');
        $this->assertNotNull($gtinProductId);
        $this->assertSame('1111111111116', $this->fetchOne('SELECT ean13 FROM {p}product WHERE id_product = :id', ['id' => $gtinProductId]), 'gtin must win over the ean13 alias when both are filled');

        $eanProductId = $this->getProductIdByReference('FLD-EAN-1');
        $this->assertNotNull($eanProductId);
        $this->assertSame('3333333333338', $this->fetchOne('SELECT ean13 FROM {p}product WHERE id_product = :id', ['id' => $eanProductId]), 'ean13 must still work on its own');
    }

    private function assertCustomizationCountsCreateRealFields(): void
    {
        $productId = $this->getProductIdByReference('FLD-CUST-1');
        $this->assertNotNull($productId);

        // 2 file fields + 1 text field, numbered generic labels
        $fields = $this->fetchAll('SELECT cf.type, cfl.name FROM {p}customization_field cf INNER JOIN {p}customization_field_lang cfl ON cfl.id_customization_field = cf.id_customization_field AND cfl.id_lang = 1 WHERE cf.id_product = :id GROUP BY cf.id_customization_field ORDER BY cf.type, cf.id_customization_field', ['id' => $productId]);
        $this->assertSame(
            [['type' => '0', 'name' => 'Customization #1'], ['type' => '0', 'name' => 'Customization #2'], ['type' => '1', 'name' => 'Customization #3']],
            array_map(static fn (array $row): array => ['type' => (string) $row['type'], 'name' => $row['name']], $fields)
        );

        // the handler maintains the product counters
        $product = $this->fetchRow('SELECT customizable, uploadable_files, text_fields FROM {p}product WHERE id_product = :id', ['id' => $productId]);
        $this->assertSame('2', (string) $product['uploadable_files']);
        $this->assertSame('1', (string) $product['text_fields']);
    }

    /**
     * @param list<ImportMessage> $messages
     */
    private function assertBothReductionsAreDropped(array $messages): void
    {
        $productId = $this->getProductIdByReference('FLD-RED-1');
        $this->assertNotNull($productId);
        $this->assertSame(0, (int) $this->fetchOne('SELECT COUNT(*) FROM {p}specific_price WHERE id_product = :id', ['id' => $productId]), 'A row with both reduction kinds must not create a specific price');

        $reductionWarnings = array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => 'reduction_price' === $message->field
        ));
        $this->assertCount(1, $reductionWarnings);
        $this->assertStringContainsString('mutually exclusive', $reductionWarnings[0]->message);
    }
}
