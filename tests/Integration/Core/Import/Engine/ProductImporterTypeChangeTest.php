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

/**
 * An is_virtual cell converts an existing product, and ProductTypeUpdater makes
 * that conversion DESTRUCTIVE for some source types: a product with
 * combinations loses every combination and has its stock reset, a pack loses its
 * contents.
 *
 * The import does perform the conversion — it is what the file asked for — but
 * the pausing validation phase warns first, so the merchant can cancel the run
 * before anything is written. These tests pin both halves: the warning is raised
 * with no writes, and the type is only touched when it actually changes.
 */
class ProductImporterTypeChangeTest extends AbstractProductImportEngineTestCase
{
    private const FIELDS = ['reference', 'is_virtual'];

    /** A default-catalog product that has combinations (and therefore stock rows per combination) */
    private const COMBINATIONS_PRODUCT_ID = 1;

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

    /**
     * The validation phase warns and writes nothing: the combinations are still
     * there after it runs, and the message names what the conversion would cost
     * (wording reused from the back office type-switch modal).
     */
    public function testConvertingAProductWithCombinationsWarnsBeforeAnyWrite(): void
    {
        $this->tagProductForConversion();
        $combinationCountBefore = $this->countCombinations();
        $this->assertGreaterThan(0, $combinationCountBefore, 'The fixture product must have combinations for this test to mean anything');

        [, $messages] = $this->runImport(
            'product_virtual_conversion.csv',
            self::FIELDS,
            ['matchRef' => true],
            [ImportPhaseDefinition::PHASE_VALIDATION]
        );

        $warnings = array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => 'is_virtual' === $message->field
        ));
        $this->assertCount(1, $warnings, 'Converting a product with combinations must warn');
        $this->assertStringContainsString('This will delete all combinations.', $warnings[0]->message);
        $this->assertStringContainsString('This will reset the stock of this product.', $warnings[0]->message);

        $this->assertSame($combinationCountBefore, $this->countCombinations(), 'The validation phase must not write anything');
    }

    /**
     * A product that is ALREADY virtual produces no warning and no command: the
     * type has not changed, so there is nothing to warn about and nothing to
     * update (dispatching anyway would cost an all-shops write on every row).
     */
    public function testConvertingAnAlreadyVirtualProductIsANoOp(): void
    {
        $this->tagProductForConversion();

        // first run converts it
        $this->runImport('product_virtual_conversion.csv', self::FIELDS, ['matchRef' => true]);
        $this->assertSame('virtual', (string) $this->fetchOne(
            'SELECT product_type FROM {p}product WHERE id_product = :id',
            ['id' => self::COMBINATIONS_PRODUCT_ID]
        ));

        // second run has nothing left to change
        [, $messages] = $this->runImport('product_virtual_conversion.csv', self::FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $typeWarnings = array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => 'is_virtual' === $message->field
        );
        $this->assertSame([], $typeWarnings, 'An unchanged type must not warn');
        $this->assertSame('virtual', (string) $this->fetchOne(
            'SELECT product_type FROM {p}product WHERE id_product = :id',
            ['id' => self::COMBINATIONS_PRODUCT_ID]
        ));
    }

    /**
     * The fixture matches by reference, so the target product needs a known one.
     */
    private function tagProductForConversion(): void
    {
        ProductResetter::resetProducts();
        $this->connection->executeStatement(
            str_replace('{p}', $this->dbPrefix, 'UPDATE {p}product SET reference = :reference WHERE id_product = :id'),
            ['reference' => 'CONV-COMB-1', 'id' => self::COMBINATIONS_PRODUCT_ID]
        );
    }

    private function countCombinations(): int
    {
        return (int) $this->fetchOne(
            'SELECT COUNT(*) FROM {p}product_attribute WHERE id_product = :id',
            ['id' => self::COMBINATIONS_PRODUCT_ID]
        );
    }
}
