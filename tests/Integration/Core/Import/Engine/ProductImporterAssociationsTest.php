<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ProductImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use Tests\Resources\Resetter\ProductResetter;

class ProductImporterAssociationsTest extends AbstractProductImportEngineTestCase
{
    private const FIELDS = ['name', 'reference', 'accessories'];

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

    public function testMutualAccessoriesInOneFile(): void
    {
        [, $messages] = $this->runImport('product_accessories_mutual.csv', self::FIELDS);

        $idA = $this->getProductIdByReference('ACC-A');
        $idB = $this->getProductIdByReference('ACC-B');
        $idC = $this->getProductIdByReference('ACC-C');
        $this->assertNotNull($idA);
        $this->assertNotNull($idB);
        $this->assertNotNull($idC);

        // A <-> B both ways although B did not exist when A's row was read
        $this->assertSame([$idB], $this->getAccessoryIds($idA));
        $this->assertSame([$idA], $this->getAccessoryIds($idB));

        // the association_validation phase warned about the unknown target
        // before any link was written
        $precheckWarnings = array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => 'accessories' === $message->field && ProductImporter::PHASE_ASSOCIATION_VALIDATION === $message->phase
        ));
        $this->assertCount(1, $precheckWarnings);
        $this->assertStringContainsString('UNKNOWN-TARGET-REF', $precheckWarnings[0]->message);

        // the association phase reported the dropped link as an error, run completed
        $associationErrors = array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_ERROR),
            static fn (ImportMessage $message): bool => ImportPhaseDefinition::PHASE_ASSOCIATION === $message->phase
        ));
        $this->assertCount(1, $associationErrors);
        $this->assertStringContainsString('UNKNOWN-TARGET-REF', $associationErrors[0]->message);
        $this->assertSame([], $this->getAccessoryIds($idC));
    }

    public function testAccessoriesPhasesAreSkippedWhenTheColumnIsNotMapped(): void
    {
        $context = $this->buildContext('product_accessories_mutual.csv', ['name', 'reference']);
        $importer = $this->getEntityImporter();

        $this->assertSame(0, $importer->countPhaseUnits(ProductImporter::PHASE_ASSOCIATION_VALIDATION, $context));
        $this->assertSame(0, $importer->countPhaseUnits(ImportPhaseDefinition::PHASE_ASSOCIATION, $context));
        $this->assertGreaterThan(0, $importer->countPhaseUnits(ImportPhaseDefinition::PHASE_VALIDATION, $context));
    }

    public function testClearMarkerEmptiesTheAssociation(): void
    {
        // ensure ACC-A exists with accessories, then clear them
        $this->runImport('product_accessories_mutual.csv', self::FIELDS, ['matchRef' => true]);
        $idA = $this->getProductIdByReference('ACC-A');
        $this->assertNotSame([], $this->getAccessoryIds($idA));

        [, $messages] = $this->runImport('product_accessories_clear.csv', self::FIELDS, ['matchRef' => true]);
        $this->assertNoErrors($messages);

        $this->assertSame([], $this->getAccessoryIds($idA), 'The @clear@ marker must empty the accessories');
    }

    /**
     * The pausing pre-check exists to PREDICT what the association phase will
     * do, so it has to resolve the owner even for a @clear@ cell: clearing also
     * needs an owner, and the write phase turns a missing one into an error.
     * Without the shared classifier the pre-check stayed silent here and the
     * error only surfaced after the pause, which is the surprise the phase was
     * added to remove.
     */
    public function testClearMarkerWithAnUnidentifiableOwnerIsReportedByThePreCheck(): void
    {
        ProductResetter::resetProducts();

        // no id column and match_ref off: the row is created, but the
        // association phases cannot re-derive its owner from the file
        [, $messages] = $this->runImport(
            'product_accessories_clear_unknown_owner.csv',
            ['name', 'reference', 'accessories'],
            [],
            [ProductImporter::PHASE_ASSOCIATION_VALIDATION]
        );

        $ownerWarnings = array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => str_contains($message->message, 'accessories owner could not be identified')
        );
        $this->assertNotEmpty($ownerWarnings, 'The pre-check must warn about the unidentifiable owner of a @clear@ row');
        $this->assertSame(
            ProductImporter::PHASE_ASSOCIATION_VALIDATION,
            reset($ownerWarnings)->phase
        );
    }

    public function testNumericAccessoryTargetsIdWinsWithReferenceFallback(): void
    {
        ProductResetter::resetProducts();
        [, $messages] = $this->runImport('product_accessories_numeric.csv', ['id', 'name', 'reference', 'accessories'], ['forceIds' => true]);

        $ownerAmbiguousId = $this->getProductIdByReference('NUM-OWNER-1');
        $ownerFallbackId = $this->getProductIdByReference('NUM-OWNER-2');
        $refFallbackTargetId = $this->getProductIdByReference('777');
        $this->assertNotNull($ownerAmbiguousId);
        $this->assertNotNull($ownerFallbackId);
        $this->assertNotNull($refFallbackTargetId);

        // '9001' matches BOTH the forced product id and another product's
        // reference: the id wins, a warning reports the ambiguity
        $this->assertSame([9001], $this->getAccessoryIds($ownerAmbiguousId));
        $ambiguityWarnings = $this->warningsContaining($messages, 'matches both a product id and a product reference');
        $this->assertNotEmpty($ambiguityWarnings, 'The id/reference ambiguity must be warned');

        // '777' matches no product id: resolved by reference, with a warning
        $this->assertSame([$refFallbackTargetId], $this->getAccessoryIds($ownerFallbackId));
        $fallbackWarnings = $this->warningsContaining($messages, 'matches no product id');
        $this->assertNotEmpty($fallbackWarnings, 'The reference fallback must be warned');
    }

    /**
     * product.reference has no unique constraint, so a reference can match several
     * products. The severity depends on the blast radius: an ambiguous LINK warns
     * and uses the lowest id, while an ambiguous IDENTITY (match_ref) fails the row
     * rather than update an arbitrary product.
     */
    public function testDuplicateReferenceWarnsOnLinksAndFailsTheMatchRefIdentity(): void
    {
        ProductResetter::resetProducts();

        [, $messages] = $this->runImport('product_duplicate_reference.csv', ['id', 'name', 'reference', 'accessories'], ['forceIds' => true]);
        $this->assertNoErrors($messages);

        // two products share DUP-REF, so the accessory link is ambiguous
        $this->assertSame([9301, 9302], $this->getProductIdsByReference('DUP-REF'));
        $ownerId = $this->getProductIdByReference('DUP-OWNER');
        $this->assertNotNull($ownerId);
        $this->assertSame([9301], $this->getAccessoryIds($ownerId), 'The lowest matching id must be linked');
        $this->assertNotEmpty(
            $this->warningsContaining($messages, 'matches 2 products'),
            'An accessory target matching several products must be warned, with the match count'
        );

        // the SAME ambiguity on the identity column is an error, not a warning:
        // match_ref would otherwise rename an arbitrary one of the two products
        [, $messages] = $this->runImport('product_duplicate_reference_match.csv', ['reference', 'name'], ['matchRef' => true]);

        $errors = $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_ERROR);
        $this->assertNotEmpty($errors, 'An ambiguous match_ref reference must fail the row');
        $this->assertStringContainsString('matches 2 products', $errors[0]->message);
        $this->assertSame('reference', $errors[0]->field);
        $this->assertSame(ImportPhaseDefinition::PHASE_VALIDATION, $errors[0]->phase, 'The error must be raised by the pausing validation phase, before any write');

        // neither product was renamed, and no third one was created
        $this->assertSame([9301, 9302], $this->getProductIdsByReference('DUP-REF'));
        $this->assertSame('Duplicate Reference One', (string) $this->fetchOne('SELECT name FROM {p}product_lang WHERE id_product = 9301 AND id_lang = 1 AND id_shop = 1'));
        $this->assertSame('Duplicate Reference Two', (string) $this->fetchOne('SELECT name FROM {p}product_lang WHERE id_product = 9302 AND id_lang = 1 AND id_shop = 1'));
    }

    /**
     * @param list<ImportMessage> $messages
     *
     * @return list<ImportMessage>
     */
    private function warningsContaining(array $messages, string $needle): array
    {
        return array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => str_contains($message->message, $needle)
        ));
    }

    /**
     * @return list<int>
     */
    /**
     * A row that identified its product by FORCED ID must keep its accessories,
     * even when another product already carries the reference the row also
     * writes. The owner is re-derived reference-first (match_ref may have chosen
     * by reference), so the shop-scoped lookup returns both products, lowest id
     * first — and without promoting the caller's id the links would land on the
     * unrelated homonym instead of the product the row actually touched.
     */
    public function testForcedIdOwnerKeepsItsAccessoriesDespiteAReferenceHomonym(): void
    {
        ProductResetter::resetProducts();

        // an existing product carrying the reference, with a LOWER id than the
        // forced one the next file uses, plus the accessory target
        [, $messages] = $this->runImport('product_owner_homonym_setup.csv', ['name', 'reference']);
        $this->assertNoErrors($messages);
        $homonymId = $this->getProductIdByReference('OWNER-DUP');
        $targetId = $this->getProductIdByReference('OWNER-TARGET');
        $this->assertNotNull($homonymId);
        $this->assertNotNull($targetId);
        $this->assertLessThan(9401, $homonymId, 'The homonym must have the lower id for this test to mean anything');

        // match_ref OFF, so the row is identified by its forced id alone - and it
        // writes OWNER-DUP too, so two in-scope products then carry it
        [, $messages] = $this->runImport(
            'product_owner_forced_id.csv',
            ['id', 'name', 'reference', 'accessories'],
            ['forceIds' => true]
        );
        $this->assertNoErrors($messages);
        $this->assertSame([$homonymId, 9401], $this->getProductIdsByReference('OWNER-DUP'));

        $this->assertSame([$targetId], $this->getAccessoryIds(9401), 'The accessories belong to the product the row identified');
        $this->assertSame([], $this->getAccessoryIds($homonymId), 'The unrelated homonym must not receive them');

        // the ambiguity is still reported: the fix reorders the matches, it does
        // not hide that a second product carries the reference
        $this->assertNotEmpty(
            $this->warningsContaining($messages, 'matches 2 products'),
            'The reference ambiguity must still be warned about'
        );
    }

    private function getAccessoryIds(?int $productId): array
    {
        $rows = $this->fetchAll('SELECT id_product_2 FROM {p}accessory WHERE id_product_1 = :id ORDER BY id_product_2', ['id' => $productId]);

        return array_map(static fn (array $row): int => (int) $row['id_product_2'], $rows);
    }
}
