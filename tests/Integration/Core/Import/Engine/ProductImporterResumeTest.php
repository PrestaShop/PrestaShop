<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ProductImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunOptions;
use PrestaShop\PrestaShop\Core\Import\Engine\PhaseBatchResult;
use Tests\Resources\Resetter\ProductResetter;

/**
 * The whole batching design rests on being able to stop after N rows and pick up
 * where it left off IN A LATER HTTP REQUEST — where the run context is rebuilt
 * from the database row and every service is constructed afresh.
 *
 * The other tests drive the importer through the mini-sequencer, which keeps ONE
 * context and ONE set of services alive for the whole run; that exercises the
 * byte-offset cursor but not the serialization boundary around it. This test
 * closes that gap: after each batch only (offset, resumeCursor) survive, the
 * kernel is rebooted so the importer and all its caches are new, and the run
 * still has to produce exactly the same result as a single-context run.
 */
class ProductImporterResumeTest extends AbstractProductImportEngineTestCase
{
    private const FIELDS = ['name', 'reference', 'price_tex'];
    private const BATCH_LIMIT = 2;

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
     * Five rows, two at a time, with a simulated request boundary between every
     * batch: nothing but the row offset and the opaque cursor crosses it.
     */
    public function testTheDatabasePhaseResumesAcrossRequestBoundaries(): void
    {
        $workingFilePath = $this->normalizeFixtureToWorkingFile('product_resume.csv');
        $recordCount = 5;

        $offset = 0;
        $resumeCursor = null;
        $processedBatches = 0;

        while ($offset < $recordCount) {
            // a NEW request: fresh kernel, fresh importer, fresh caches, and a
            // context rebuilt from the two values a database row would hold
            self::ensureKernelShutdown();
            self::bootKernel();

            $context = $this->buildResumableContext($workingFilePath, $recordCount);
            $context->enterPhase(ImportPhaseDefinition::PHASE_DATABASE, $recordCount);
            // replay the persisted progress the way the PR2 sequencer will:
            // enterPhase() zeroes it, this puts back what the run row held
            $context->applyBatchResult(new PhaseBatchResult($offset, [], [], $resumeCursor));

            $this->assertSame($offset, $context->getCurrentOffset());
            $this->assertSame($resumeCursor, $context->getResumeCursor());

            $importer = self::getContainer()->get(ProductImporter::class);
            $result = $importer->processPhaseBatch(ImportPhaseDefinition::PHASE_DATABASE, $context, self::BATCH_LIMIT);

            $this->assertGreaterThan(0, $result->processedUnitCount, 'A batch must make progress or the run would never end');
            $this->assertNotNull($result->resumeCursor, 'Every batch must hand back a cursor for the next request');

            $offset += $result->processedUnitCount;
            $resumeCursor = $result->resumeCursor;
            ++$processedBatches;
        }

        $this->assertSame(3, $processedBatches, '5 rows at 2 per batch means 3 requests');
        $this->assertSame($recordCount, $offset);

        // every row imported exactly once: no row skipped by a bad seek, none
        // imported twice by a reset cursor
        foreach (['RES-1', 'RES-2', 'RES-3', 'RES-4', 'RES-5'] as $index => $reference) {
            $this->assertCount(
                1,
                $this->getProductIdsByReference($reference),
                sprintf('Row %d must have produced exactly one product', $index)
            );
        }
        $this->assertSame('30.000000', (string) $this->fetchOne(
            'SELECT price FROM {p}product_shop WHERE id_product = :id AND id_shop = 1',
            ['id' => $this->getProductIdByReference('RES-3')]
        ), 'The row straddling the second boundary must keep its own values');
    }

    /**
     * Normalizes the fixture once, the way StartImportRun will: the working file
     * outlives the request boundaries, only the cursor into it travels.
     */
    private function normalizeFixtureToWorkingFile(string $fixtureName): string
    {
        return $this->buildContext($fixtureName, self::FIELDS)->getWorkingFilePath();
    }

    private function buildResumableContext(string $workingFilePath, int $recordCount): ImportRunContext
    {
        return new ImportRunContext(
            ProductImporter::ENTITY_TYPE,
            $workingFilePath,
            $recordCount,
            static::DEFAULT_LANG_ISO,
            ',',
            self::FIELDS,
            ImportRunOptions::fromArray([]),
            ShopConstraint::shop(static::DEFAULT_SHOP_ID)
        );
    }
}
