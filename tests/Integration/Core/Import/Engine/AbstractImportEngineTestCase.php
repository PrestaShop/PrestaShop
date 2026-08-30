<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\File\CsvImportFileNormalizer;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunOptions;
use SplFileInfo;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Tests\Integration\Utility\ContextMockerTrait;

/**
 * Entity-agnostic harness: normalize a fixture, build a run context and drive
 * an importer through the mini batch sequencer. Entity-specific test cases
 * provide the importer (see AbstractProductImportEngineTestCase).
 *
 * Why mockContext(): measured by running the whole suite without it, exactly
 * one command-dispatch path still reads the legacy context — stock movements.
 * UpdateProductStockAvailableCommand (delta quantity) ends in
 * StockManager::saveMovement(), which reads Context::getContext()->employee
 * to stamp the movement; without the mock the employee is an unloaded shell
 * whose null id fatals in StockMvt::setIdEmployee(). Every other phase/command
 * chain covered here runs fine on the raw kernel context; the mock also pins
 * shop/language/currency so assertions stay deterministic.
 */
abstract class AbstractImportEngineTestCase extends KernelTestCase
{
    use ContextMockerTrait;

    protected const DEFAULT_SHOP_ID = 1;
    protected const DEFAULT_LANG_ISO = 'en';

    protected Connection $connection;

    protected string $dbPrefix;

    /**
     * @var list<string>
     */
    private array $temporaryFiles = [];

    /**
     * @var list<string>
     */
    private array $temporaryDirectories = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        static::backupContext();
        static::mockContext();
    }

    public static function tearDownAfterClass(): void
    {
        static::resetContext();
        parent::tearDownAfterClass();
    }

    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
        // some legacy code paths resolve services through SymfonyContainer::getInstance()
        global $kernel;
        $kernel = self::$kernel;

        $this->connection = self::getContainer()->get('doctrine.dbal.default_connection');
        $this->dbPrefix = self::getContainer()->getParameter('database_prefix');
    }

    protected function tearDown(): void
    {
        // Filesystem::remove() takes an iterable, is recursive and never
        // complains about what is already gone
        (new Filesystem())->remove([...$this->temporaryFiles, ...$this->temporaryDirectories]);
        $this->temporaryFiles = [];
        $this->temporaryDirectories = [];

        parent::tearDown();
    }

    /**
     * Normalizes a fixture into a working file and builds a run context for it.
     *
     * @param array<int, string> $fieldMapping column index => field name
     * @param array<string, bool> $options
     */
    protected function buildContext(
        string $fixtureName,
        array $fieldMapping,
        array $options = [],
        int $skipRows = 1,
        string $sourceCsvSeparator = ';',
        string $multipleValueSeparator = ',',
        ?ShopConstraint $shopConstraint = null
    ): ImportRunContext {
        $fixturePath = $this->prepareFixture($fixtureName);

        // skip rows (fixture headers) are consumed here, like the separator:
        // the working file contains data records only, and the record count
        // is measured by the same pass
        $workingFilePath = $this->createTemporaryFilePath('work_', '.csv');
        $normalizer = new CsvImportFileNormalizer(new Filesystem());
        $normalizedFile = $normalizer->normalize(new SplFileInfo($fixturePath), $workingFilePath, $sourceCsvSeparator, $skipRows);

        return new ImportRunContext(
            $this->getEntityImporter()->getEntityType(),
            $workingFilePath,
            $normalizedFile->dataRecordCount,
            static::DEFAULT_LANG_ISO,
            $multipleValueSeparator,
            $fieldMapping,
            ImportRunOptions::fromArray($options),
            $shopConstraint ?? ShopConstraint::shop(static::DEFAULT_SHOP_ID)
        );
    }

    /**
     * Runs a fixture through the importer (all phases unless restricted).
     *
     * @param array<int, string> $fieldMapping
     * @param array<string, bool> $options
     * @param list<string>|null $phaseIds
     *
     * @return array{0: ImportRunContext, 1: list<ImportMessage>}
     */
    protected function runImport(
        string $fixtureName,
        array $fieldMapping,
        array $options = [],
        ?array $phaseIds = null,
        int $batchLimit = 2
    ): array {
        $context = $this->buildContext($fixtureName, $fieldMapping, $options);
        $importer = $this->getEntityImporter();

        $messages = (new ImportEngineTestRunner())->run($importer, $context, $batchLimit, $phaseIds);

        return [$context, $messages];
    }

    /**
     * The importer under test, usually fetched from the container.
     */
    abstract protected function getEntityImporter(): EntityImporterInterface;

    /**
     * Copies the fixture to a temporary location and resolves the {FIXTURE_DIR}
     * placeholder so fixtures can reference bundled files (images, downloads).
     *
     * The placeholder does NOT resolve to tests/Resources directly: FileDownloader
     * confines local paths to the shop CONTENT directories plus the system temp
     * dir, and the bundled fixture assets live in neither. Every referenced asset
     * is therefore staged into a temp directory (an allowed root) and the
     * placeholder points there — so the tests exercise the real confinement
     * instead of needing it widened for their own convenience.
     */
    protected function prepareFixture(string $fixtureName): string
    {
        $fixturePath = $this->getFixtureDir() . '/import/' . $fixtureName;
        static::assertFileExists($fixturePath);

        $filesystem = new Filesystem();
        // Filesystem has no reader before Symfony 7.1
        $content = (string) file_get_contents($fixturePath);
        if (!str_contains($content, '{FIXTURE_DIR}')) {
            return $fixturePath;
        }

        $substitutedPath = $this->createTemporaryFilePath('fixture_', '.csv');
        $filesystem->dumpFile($substitutedPath, str_replace('{FIXTURE_DIR}', $this->stageReferencedAssets($content), $content));

        return $substitutedPath;
    }

    /**
     * Copies every {FIXTURE_DIR}-relative asset the fixture references into a
     * temp staging directory, keeping the relative sub-path so the substitution
     * stays a plain string replacement.
     *
     * @return string the staging directory, without a trailing separator
     */
    private function stageReferencedAssets(string $content): string
    {
        $filesystem = new Filesystem();
        $stagingDirectory = sys_get_temp_dir() . '/' . uniqid('ps_import_assets_', true);
        // mkdir() is recursive and idempotent, and throws on real failures
        $filesystem->mkdir($stagingDirectory);
        $this->temporaryDirectories[] = $stagingDirectory;

        preg_match_all('#\{FIXTURE_DIR\}(/[^;,\r\n"]+)#', $content, $matches);
        foreach (array_unique($matches[1]) as $relativePath) {
            $source = $this->getFixtureDir() . $relativePath;
            if (!is_file($source)) {
                continue;
            }

            // flattening is not an option (two assets could share a basename),
            // so mirror the sub-directories inside the staging dir; copy()
            // creates the parent directory itself
            $filesystem->copy($source, $stagingDirectory . $relativePath, true);
        }

        return $stagingDirectory;
    }

    protected function getFixtureDir(): string
    {
        return dirname(__DIR__, 4) . '/Resources';
    }

    protected function createTemporaryFilePath(string $prefix, string $suffix): string
    {
        $path = sys_get_temp_dir() . '/' . uniqid($prefix, true) . $suffix;
        $this->temporaryFiles[] = $path;

        return $path;
    }

    /**
     * @param list<ImportMessage> $messages
     *
     * @return list<ImportMessage>
     */
    protected function messagesOfSeverity(array $messages, string $severity): array
    {
        return array_values(array_filter(
            $messages,
            static fn (ImportMessage $message): bool => $severity === $message->severity
        ));
    }

    /**
     * @param list<ImportMessage> $messages
     */
    protected function assertNoErrors(array $messages): void
    {
        $errors = $this->messagesOfSeverity($messages, ImportMessage::SEVERITY_ERROR);
        static::assertSame(
            [],
            array_map(static fn (ImportMessage $message): string => sprintf('[row %s][%s] %s', $message->row ?? '-', $message->field ?? '-', $message->message), $errors),
            'The import produced unexpected errors'
        );
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return array<string, mixed>|false
     */
    protected function fetchRow(string $sql, array $parameters = [])
    {
        return $this->connection->fetchAssociative(str_replace('{p}', $this->dbPrefix, $sql), $parameters);
    }

    /**
     * @param array<string, mixed> $parameters
     */
    protected function fetchOne(string $sql, array $parameters = [])
    {
        return $this->connection->fetchOne(str_replace('{p}', $this->dbPrefix, $sql), $parameters);
    }

    /**
     * @param array<string, mixed> $parameters
     *
     * @return list<array<string, mixed>>
     */
    protected function fetchAll(string $sql, array $parameters = []): array
    {
        return $this->connection->fetchAllAssociative(str_replace('{p}', $this->dbPrefix, $sql), $parameters);
    }
}
