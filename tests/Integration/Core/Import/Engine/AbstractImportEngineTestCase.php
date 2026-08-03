<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Core\Import\Engine;

use Doctrine\DBAL\Connection;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporter\ProductImporter;
use PrestaShop\PrestaShop\Core\Import\Engine\File\ImportFileNormalizer;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportMessage;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunContext;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportRunOptions;
use SplFileInfo;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Tests\Integration\Utility\ContextMockerTrait;

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
        foreach ($this->temporaryFiles as $temporaryFile) {
            @unlink($temporaryFile);
        }
        $this->temporaryFiles = [];

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
        string $sourceCsvSeparator = ';'
    ): ImportRunContext {
        $fixturePath = $this->prepareFixture($fixtureName);

        $workingFilePath = $this->createTemporaryFilePath('work_', '.csv');
        $normalizer = new ImportFileNormalizer();
        $normalizer->normalize(new SplFileInfo($fixturePath), $workingFilePath, $sourceCsvSeparator);

        return new ImportRunContext(
            ProductImporter::ENTITY_TYPE,
            $workingFilePath,
            static::DEFAULT_LANG_ISO,
            $sourceCsvSeparator,
            ',',
            $skipRows,
            $fieldMapping,
            ImportRunOptions::fromArray($options),
            static::DEFAULT_SHOP_ID
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
        $importer = $this->getProductImporter();

        $messages = (new ImportEngineTestRunner())->run($importer, $context, $batchLimit, $phaseIds);

        return [$context, $messages];
    }

    protected function getProductImporter(): ProductImporter
    {
        return self::getContainer()->get(ProductImporter::class);
    }

    /**
     * Copies the fixture to a temporary location, substituting the
     * {FIXTURE_DIR} placeholder with the absolute tests/Resources path so
     * fixtures can reference bundled files (images, downloads).
     */
    protected function prepareFixture(string $fixtureName): string
    {
        $fixturePath = $this->getFixtureDir() . '/import/' . $fixtureName;
        static::assertFileExists($fixturePath);

        $content = (string) file_get_contents($fixturePath);
        if (!str_contains($content, '{FIXTURE_DIR}')) {
            return $fixturePath;
        }

        $substitutedPath = $this->createTemporaryFilePath('fixture_', '.csv');
        file_put_contents($substitutedPath, str_replace('{FIXTURE_DIR}', $this->getFixtureDir(), $content));

        return $substitutedPath;
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

    protected function getProductIdByReference(string $reference): ?int
    {
        $productId = $this->fetchOne('SELECT id_product FROM {p}product WHERE reference = :reference', ['reference' => $reference]);

        return false === $productId ? null : (int) $productId;
    }
}
