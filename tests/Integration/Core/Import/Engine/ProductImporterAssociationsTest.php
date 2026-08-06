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

        // pre-check warned about the unknown target during validation
        $precheckWarnings = array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => 'accessories' === $message->field && ImportPhaseDefinition::PHASE_VALIDATION === $message->phase
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

    public function testSkipAssociationPrecheckSuppressesTheValidationWarning(): void
    {
        [, $messages] = $this->runImport('product_accessories_mutual.csv', self::FIELDS, ['matchRef' => true, 'skipAssociationPrecheck' => true]);

        $precheckWarnings = array_values(array_filter(
            $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_WARNING),
            static fn (ImportMessage $message): bool => 'accessories' === $message->field && ImportPhaseDefinition::PHASE_VALIDATION === $message->phase
        ));
        $this->assertSame([], $precheckWarnings);
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
    private function getAccessoryIds(?int $productId): array
    {
        $rows = $this->fetchAll('SELECT id_product_2 FROM {p}accessory WHERE id_product_1 = :id ORDER BY id_product_2', ['id' => $productId]);

        return array_map(static fn (array $row): int => (int) $row['id_product_2'], $rows);
    }
}
