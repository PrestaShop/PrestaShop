<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use SplFileInfo;
use Symfony\Component\Stopwatch\Stopwatch;
use Tests\Resources\Resetter\ProductResetter;

/**
 * Hook-per-command benchmark (PLAN.md PR1 checklist): hooks fire on every
 * CQRS command while the legacy import deferred them via Module::setBatchMode.
 * This measures the accepted cost on a generated fixture.
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
    private const FIELDS = ['name', 'reference', 'price_tex', 'quantity', 'active'];

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
        }
        parent::tearDownAfterClass();
    }

    public function testDatabasePhaseThroughput(): void
    {
        $fixturePath = $this->createTemporaryFilePath('bench_', '.csv');
        $handle = fopen($fixturePath, 'wb');
        fputcsv($handle, ['name', 'reference', 'price_tex', 'quantity', 'active'], ';', '"', '');
        for ($i = 1; $i <= self::ROW_COUNT; ++$i) {
            fputcsv($handle, ['Bench Product ' . $i, 'BENCH-' . $i, '9.99', '10', '1'], ';', '"', '');
        }
        fclose($handle);

        $workingFilePath = $this->createTemporaryFilePath('bench_work_', '.csv');
        $normalizedFile = (new \PrestaShop\PrestaShop\Core\Import\Engine\File\CsvImportFileNormalizer())
            ->normalize(new SplFileInfo($fixturePath), $workingFilePath, ';', 1);
        $context = new \PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext(
            'product',
            $workingFilePath,
            $normalizedFile->dataRecordCount,
            self::DEFAULT_LANG_ISO,
            ';',
            ',',
            self::FIELDS,
            new \PrestaShop\PrestaShop\Core\Import\Engine\ImportRunOptions(),
            self::DEFAULT_SHOP_ID
        );

        $stopwatch = new Stopwatch(true);
        $stopwatch->start('import');
        $messages = (new ImportEngineTestRunner())->run($this->getProductImporter(), $context, 100);
        $event = $stopwatch->stop('import');

        $this->assertNoErrors($messages);

        $seconds = $event->getDuration() / 1000;
        fwrite(STDOUT, sprintf(
            "\nImport benchmark: %d rows in %.2f s (%.1f rows/s), %.1f MB peak\n",
            self::ROW_COUNT,
            $seconds,
            self::ROW_COUNT / max($seconds, 0.001),
            $event->getMemory() / 1024 / 1024
        ));
    }
}
