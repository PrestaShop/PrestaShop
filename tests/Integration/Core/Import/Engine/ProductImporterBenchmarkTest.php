<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\File\CsvImportFileNormalizer;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunOptions;
use SplFileInfo;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Stopwatch\Stopwatch;
use Tests\Resources\DatabaseDump;
use Tests\Resources\Resetter\ProductResetter;

/**
 * Hook-per-command benchmark (PLAN.md PR1 checklist): hooks fire on every
 * CQRS command while the legacy import deferred them via Module::setBatchMode.
 * Two scenarios on generated fixtures:
 * - scalar: plain product fields only, the historical baseline (the
 *   accessories phases are skipped — countPhaseUnits() returns 0)
 * - associations: categories, manufacturers, features and accessories, so
 *   the association_validation and association phases run for every row
 *
 * SKIPPED in regular suite runs: setUp() calls markTestSkipped() unless the
 * IMPORT_BENCHMARK environment variable is set. Run it manually:
 *
 *   IMPORT_BENCHMARK=1 php vendor/bin/phpunit -c tests/Integration/phpunit.xml \
 *     --filter ProductImporterBenchmarkTest
 *
 * Record the numbers in .ai/Component/Import/PLAN.md.
 */
class ProductImporterBenchmarkTest extends AbstractProductImportEngineTestCase
{
    private const ROW_COUNT = 1000;
    private const BATCH_LIMIT = 100;
    private const SCALAR_FIELDS = ['name', 'reference', 'price_tex', 'quantity', 'active'];
    private const ASSOCIATION_FIELDS = ['name', 'reference', 'price_tex', 'category', 'manufacturer', 'features', 'accessories'];

    protected function setUp(): void
    {
        if (!getenv('IMPORT_BENCHMARK')) {
            $this->markTestSkipped('Set IMPORT_BENCHMARK=1 to run the import benchmark');
        }
        parent::setUp();
    }

    public static function tearDownAfterClass(): void
    {
        if (getenv('IMPORT_BENCHMARK')) {
            ProductResetter::resetProducts();
            DatabaseDump::restoreTables(['category', 'category_lang', 'category_shop', 'category_group', 'manufacturer', 'manufacturer_lang', 'manufacturer_shop', 'feature', 'feature_lang', 'feature_shop', 'feature_value', 'feature_value_lang']);
        }
        parent::tearDownAfterClass();
    }

    public function testScalarThroughput(): void
    {
        $context = $this->buildBenchmarkContext(self::SCALAR_FIELDS, static fn (int $i): array => [
            'Bench Product ' . $i,
            'BENCH-' . $i,
            '9.99',
            '10',
            '1',
        ]);

        $this->runTimedImport('scalar', $context);
    }

    /**
     * Association-heavy shape: 10 distinct auto-created categories, brands
     * and feature values rotate across the file (so the run-lifetime caches
     * work like on a real catalog), and every row references the PREVIOUS
     * row's product as an accessory (row 1 references the last one, proving
     * order independence) — the association_validation and association
     * phases probe and link every row.
     */
    public function testAssociationHeavyThroughput(): void
    {
        $context = $this->buildBenchmarkContext(self::ASSOCIATION_FIELDS, static fn (int $i): array => [
            'Bench Assoc Product ' . $i,
            'BENCH-ASSOC-' . $i,
            '9.99',
            'Bench Category ' . ($i % 10),
            'Bench Brand ' . ($i % 10),
            'Bench Feature:Bench Value ' . ($i % 10) . ':0',
            'BENCH-ASSOC-' . (1 === $i ? self::ROW_COUNT : $i - 1),
        ]);

        $this->runTimedImport('associations', $context);
    }

    /**
     * @param callable(int): array<int, string> $rowFactory generates row $i (1-based)
     */
    private function buildBenchmarkContext(array $fields, callable $rowFactory): ImportRunContext
    {
        $fixturePath = $this->createTemporaryFilePath('bench_', '.csv');
        $handle = fopen($fixturePath, 'wb');
        fputcsv($handle, $fields, ';', '"', '');
        for ($i = 1; $i <= self::ROW_COUNT; ++$i) {
            fputcsv($handle, $rowFactory($i), ';', '"', '');
        }
        fclose($handle);

        $workingFilePath = $this->createTemporaryFilePath('bench_work_', '.csv');
        $normalizedFile = (new CsvImportFileNormalizer(new Filesystem()))->normalize(new SplFileInfo($fixturePath), $workingFilePath, ';', 1);

        return new ImportRunContext(
            'product',
            $workingFilePath,
            $normalizedFile->dataRecordCount,
            self::DEFAULT_LANG_ISO,
            ',',
            $fields,
            new ImportRunOptions(),
            ShopConstraint::shop(self::DEFAULT_SHOP_ID)
        );
    }

    /**
     * Same loop as ImportEngineTestRunner, with a stopwatch section per phase
     * so the report shows where the time goes.
     */
    private function runTimedImport(string $label, ImportRunContext $context): void
    {
        $importer = $this->getProductImporter();
        $stopwatch = new Stopwatch(true);
        $messages = [];
        $phaseReports = [];

        $stopwatch->start('import');
        foreach ($importer->getPhases() as $phase) {
            $stopwatch->start($phase->id);
            $context->enterPhase($phase->id, $importer->countPhaseUnits($phase->id, $context));
            while ($context->getCurrentOffset() < $context->getCurrentPhaseTotalUnits()) {
                $result = $importer->processPhaseBatch(
                    $phase->id,
                    $context,
                    min(self::BATCH_LIMIT, $context->getCurrentPhaseTotalUnits() - $context->getCurrentOffset())
                );
                $context->applyBatchResult($result);
                $messages = array_merge($messages, $result->messages);
            }
            $phaseEvent = $stopwatch->stop($phase->id);
            $phaseReports[] = sprintf('%s: %.2f s', $phase->id, $phaseEvent->getDuration() / 1000);
        }
        $event = $stopwatch->stop('import');

        $this->assertNoErrors($messages);

        $seconds = $event->getDuration() / 1000;
        fwrite(STDOUT, sprintf(
            "\nImport benchmark [%s]: %d rows in %.2f s (%.1f rows/s), %.1f MB peak\n  %s\n",
            $label,
            self::ROW_COUNT,
            $seconds,
            self::ROW_COUNT / max($seconds, 0.001),
            $event->getMemory() / 1024 / 1024,
            implode(' | ', $phaseReports)
        ));
    }
}
