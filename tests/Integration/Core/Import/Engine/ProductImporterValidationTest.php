<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use Tests\Resources\Resetter\ProductResetter;

class ProductImporterValidationTest extends AbstractProductImportEngineTestCase
{
    private const FIELDS = ['name', 'reference', 'visibility', 'ean13', 'category', 'active', 'isbn'];

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

    public function testValidationPhasePerformsNoWrites(): void
    {
        $productCount = (int) $this->fetchOne('SELECT COUNT(*) FROM {p}product');
        $categoryCount = (int) $this->fetchOne('SELECT COUNT(*) FROM {p}category');

        [, $messages] = $this->runImport('product_invalid_rows.csv', self::FIELDS, [], [ImportPhaseDefinition::PHASE_VALIDATION]);

        $this->assertNotEmpty($messages);
        $this->assertSame($productCount, (int) $this->fetchOne('SELECT COUNT(*) FROM {p}product'), 'Validation must not create products');
        $this->assertSame($categoryCount, (int) $this->fetchOne('SELECT COUNT(*) FROM {p}category'), 'Validation must not create categories');
    }

    public function testInvalidRowsAreReportedAndSkippedWhileValidRowsAreImported(): void
    {
        [$context, $messages] = $this->runImport('product_invalid_rows.csv', self::FIELDS);

        // structured messages carry severity/phase/row/field
        $errors = $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_ERROR);
        $errorsByField = [];
        foreach ($errors as $error) {
            $this->assertSame(ImportPhaseDefinition::PHASE_VALIDATION, $error->phase);
            $this->assertNotNull($error->row);
            $errorsByField[$error->field][] = $error->row;
        }
        // row indexes are 0-based DATA-record indexes (the header was already
        // stripped at normalization); presenters add the skip count back
        $this->assertSame([1], $errorsByField['visibility'] ?? [], 'bad visibility row');
        $this->assertSame([2], $errorsByField['ean13'] ?? [], 'bad gtin row');
        $this->assertSame([3], $errorsByField['name'] ?? [], 'missing name row');
        $this->assertSame([5], $errorsByField['category'] ?? [], 'unknown numeric category row');
        $this->assertSame([7], $errorsByField['isbn'] ?? [], 'bad isbn row (full ISBN format mirrored from the Isbn value object)');

        // the message texts name the offending value / expectation
        $errorTextsByField = [];
        foreach ($errors as $error) {
            $errorTextsByField[$error->field][] = $error->message;
        }
        $this->assertStringContainsString('Invalid visibility "everywhere"', $errorTextsByField['visibility'][0]);
        $this->assertStringContainsString('Invalid GTIN', $errorTextsByField['ean13'][0]);
        $this->assertStringContainsString('name is required', $errorTextsByField['name'][0]);
        $this->assertStringContainsString('does not exist', $errorTextsByField['category'][0]);
        $this->assertStringContainsString('Invalid ISBN "ABC"', $errorTextsByField['isbn'][0]);

        // blank line = notice + skip, not an abort
        $notices = $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_NOTICE);
        $this->assertNotEmpty($notices);
        $this->assertSame(4, $notices[0]->row);

        // unparseable boolean = warning, row still goes through
        $booleanWarnings = array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => 'active' === $message->field
        ));
        $this->assertCount(1, $booleanWarnings);
        $this->assertSame(6, $booleanWarnings[0]->row);
        $this->assertStringContainsString('Unrecognized boolean', $booleanWarnings[0]->message);
        $this->assertStringContainsString('"false" will be used', $booleanWarnings[0]->message);

        // skipped rows = the 5 error rows + the blank row
        $this->assertSame([1, 2, 3, 4, 5, 7], $context->getSkippedRows(ImportPhaseDefinition::PHASE_VALIDATION));

        // valid rows imported, invalid ones absent
        $this->assertNotNull($this->getProductIdByReference('INV-OK-1'));
        $this->assertNotNull($this->getProductIdByReference('INV-FUZZY-BOOL'));
        $this->assertNull($this->getProductIdByReference('INV-BAD-VIS'));
        $this->assertNull($this->getProductIdByReference('INV-BAD-GTIN'));
        $this->assertNull($this->getProductIdByReference('INV-NO-NAME'));
        $this->assertNull($this->getProductIdByReference('INV-BAD-CAT'));
        $this->assertNull($this->getProductIdByReference('INV-BAD-ISBN'));

        // the fuzzy boolean was treated as false
        $fuzzyId = $this->getProductIdByReference('INV-FUZZY-BOOL');
        $this->assertSame('0', (string) $this->fetchOne('SELECT active FROM {p}product_shop WHERE id_product = :id AND id_shop = 1', ['id' => $fuzzyId]));
    }
}
