<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain\Import;

use Behat\Gherkin\Node\TableNode;
use DateTimeImmutable;
use Db;
use Exception;
use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\CancelImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\ContinueImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\PurgeImportJobsCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\StartImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\CannotStartImportJobException;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobAlreadyRunningException;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobStatusException;
use PrestaShop\PrestaShop\Core\Domain\Import\Query\GetImportJobState;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobMessage;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportJobState;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobPurgeSummary;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use Symfony\Component\Filesystem\Filesystem;
use Tests\Integration\Behaviour\Features\Context\Domain\AbstractDomainFeatureContext;

/**
 * Drives import jobs of ANY entity type through the command bus.
 *
 * Nothing here knows which importer is running: the entity type is a step parameter and the
 * column mapping is read from the fixture's own header row, the way a merchant maps columns whose
 * names already match the fields.
 */
class ImportJobFeatureContext extends AbstractDomainFeatureContext
{
    private const FIXTURE_DIR = __DIR__ . '/../../../../../../Resources/import/';

    /**
     * Prefixes the copy a scenario uploads. The rest of the name is the job reference, so a later
     * step finds the source again without anything having stored its path.
     */
    private const SOURCE_PREFIX = 'behat-import-';

    private const DEFAULT_SHOP_ID = 1;

    /**
     * A file is read in one language; a scenario only names it when it matters.
     */
    private const DEFAULT_LANGUAGE_ISO = 'en';

    /**
     * Copies the fixture where a real upload lands — the system temp directory, one of the two
     * roots a source may be read from — and starts the job in the same step, so no scenario
     * depends on a path some earlier step stashed away.
     *
     * @When I start an import job :reference for entity type :entityType from file :fixture
     * @When I start an import job :reference for entity type :entityType from file :fixture with following options:
     * @When I start an import job :reference for entity type :entityType from file :fixture in language :langIso
     * @When I start an import job :reference for entity type :entityType from file :fixture in language :langIso with following options:
     */
    public function startImportJob(
        string $reference,
        string $entityType,
        string $fixture,
        ?string $langIso = null,
        ?TableNode $table = null,
    ): void {
        $source = sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::SOURCE_PREFIX . $reference . '.' . pathinfo($fixture, PATHINFO_EXTENSION);
        (new Filesystem())->copy(self::FIXTURE_DIR . $fixture, $source, true);

        try {
            $importJobUuid = $this->getCommandBus()->handle(new StartImportJobCommand(
                $source,
                $entityType,
                $langIso ?? self::DEFAULT_LANGUAGE_ISO,
                ShopConstraint::shop(self::DEFAULT_SHOP_ID),
                $this->readFieldMapping($source),
                null === $table ? [] : $this->castOptions($table->getRowsHash())
            ));
        } catch (Exception $exception) {
            $this->setLastException($exception);

            return;
        }

        $this->getSharedStorage()->set($reference, $importJobUuid->getValue());
    }

    /**
     * Skips the upload copy and points straight at the fixture directory, which is neither the
     * import directory nor the system temp — the only two roots a source may be read from. A
     * fixture name that does not exist reaches the missing-file guard through the same step.
     *
     * @When I start an import job :reference for entity type :entityType from the unconfined file :fixture
     */
    public function startImportJobFromUnconfinedFile(string $reference, string $entityType, string $fixture): void
    {
        $source = self::FIXTURE_DIR . $fixture;

        try {
            $importJobUuid = $this->getCommandBus()->handle(new StartImportJobCommand(
                $source,
                $entityType,
                self::DEFAULT_LANGUAGE_ISO,
                ShopConstraint::shop(self::DEFAULT_SHOP_ID),
                is_file($source) ? $this->readFieldMapping($source) : ['verb'],
            ));
        } catch (Exception $exception) {
            $this->setLastException($exception);

            return;
        }

        $this->getSharedStorage()->set($reference, $importJobUuid->getValue());
    }

    /**
     * @When I continue the import job :reference
     * @When I continue the import job :reference with a batch limit of :batchLimit
     */
    public function continueImportJob(string $reference, ?int $batchLimit = null): void
    {
        try {
            $this->getCommandBus()->handle(new ContinueImportJobCommand($this->referenceToUuid($reference), $batchLimit));
        } catch (Exception $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * Polls the way a client does, which is also how a pause gets accepted.
     *
     * @When I continue the import job :reference until it stops
     */
    public function continueImportJobUntilItStops(string $reference): void
    {
        // generous, but finite: a sequencer that stopped making progress must fail the suite
        // rather than hang it
        for ($batch = 0; $batch < 50; ++$batch) {
            if ($this->getImportJobState($reference)->getStatus()->isTerminal()) {
                return;
            }

            try {
                $this->getCommandBus()->handle(new ContinueImportJobCommand($this->referenceToUuid($reference)));
            } catch (Exception $exception) {
                $this->setLastException($exception);

                return;
            }
        }

        Assert::fail(sprintf('Import job "%s" never reached a terminal status', $reference));
    }

    /**
     * @When I cancel the import job :reference
     */
    public function cancelImportJob(string $reference): void
    {
        try {
            $this->getCommandBus()->handle(new CancelImportJobCommand($this->referenceToUuid($reference)));
        } catch (Exception $exception) {
            $this->setLastException($exception);
        }
    }

    /**
     * Purges without looking at the result, for the scenarios whose point is the error.
     *
     * @When I purge import jobs
     * @When I purge import jobs expired before :expirationDate
     */
    public function purgeImportJobs(?string $expirationDate = null): void
    {
        $this->purge($expirationDate);
    }

    /**
     * @When I purge import jobs I should get the following result:
     */
    public function purgeImportJobsAndAssertResult(TableNode $table): void
    {
        $this->assertPurgeResult($this->purge(null), $table);
    }

    /**
     * @When I purge import jobs expired before :expirationDate I should get the following result:
     */
    public function purgeImportJobsExpiredBeforeAndAssertResult(string $expirationDate, TableNode $table): void
    {
        $this->assertPurgeResult($this->purge($expirationDate), $table);
    }

    /**
     * The retention window is days wide, so a scenario ages the row instead of waiting.
     *
     * @Given the import job :reference was last updated :days days ago
     */
    public function backDateImportJob(string $reference, int $days): void
    {
        Db::getInstance()->execute(sprintf(
            'UPDATE %simport_job SET date_upd = DATE_SUB(NOW(), INTERVAL %d DAY) WHERE import_job_uuid = "%s"',
            _DB_PREFIX_,
            $days,
            pSQL($this->referenceToUuid($reference))
        ));
    }

    /**
     * A working file whose job row never existed — a crash between normalizing and persisting.
     *
     * @Given an orphan import working file was left :days days ago
     */
    public function leaveOrphanWorkingFile(int $days): void
    {
        $workingDirectory = $this->getImportDirectory() . 'work' . DIRECTORY_SEPARATOR;
        (new Filesystem())->mkdir($workingDirectory);

        $orphan = $workingDirectory . '0198f1a4-0b3c-7c21-9a4e-000000000000.csv';
        file_put_contents($orphan, "header\n");
        touch($orphan, time() - $days * 86400);
    }

    /**
     * Reads the job back and asserts only the properties the table names, so a scenario can check
     * one thing or all of them.
     *
     * @Then import job :reference should have the following properties:
     */
    public function assertImportJobProperties(string $reference, TableNode $table): void
    {
        $state = $this->getImportJobState($reference);
        $actual = [
            'entityType' => $state->getEntityType(),
            'status' => $state->getStatus()->value,
            'currentPhaseId' => (string) $state->getCurrentPhaseId(),
            'dataRecordCount' => (string) $state->getDataRecordCount(),
            'skippedRowCount' => (string) $state->getSkippedRowCount(),
            'progressPercent' => (string) $state->getProgressPercent(),
            'errorCount' => (string) count($this->getMessagesBySeverity($state, 'error')),
            'warningCount' => (string) count($this->getMessagesBySeverity($state, 'warning')),
            'noticeCount' => (string) count($this->getMessagesBySeverity($state, 'notice')),
            'workingFile' => file_exists($this->getWorkingFilePath($reference)) ? 'present' : 'absent',
        ];

        foreach ($table->getRowsHash() as $property => $expected) {
            Assert::assertArrayHasKey($property, $actual, sprintf('Unknown import job property "%s"', $property));
            Assert::assertSame($expected, $actual[$property], sprintf('Import job property "%s"', $property));
        }
    }

    /**
     * @Then the source file of import job :reference should exist
     */
    public function assertSourceFileExists(string $reference): void
    {
        Assert::assertNotEmpty($this->findSourceFiles($reference), 'The import source file was deleted');
    }

    /**
     * @Then the source file of import job :reference should not exist
     */
    public function assertSourceFileDoesNotExist(string $reference): void
    {
        Assert::assertEmpty($this->findSourceFiles($reference), 'The import source file is still there');
    }

    /**
     * @Then import job :reference should have the following phases:
     */
    public function assertImportJobPhases(string $reference, TableNode $table): void
    {
        $phases = [];
        foreach ($this->getImportJobState($reference)->getPhases() as $phase) {
            $phases[$phase->getId()] = [
                'totalUnits' => (string) $phase->getTotalUnits(),
                'offset' => (string) $phase->getOffset(),
                'pausing' => $phase->isPausing() ? 'true' : 'false',
            ];
        }

        foreach ($table->getColumnsHash() as $expectedPhase) {
            $phaseId = $expectedPhase['id'];
            Assert::assertArrayHasKey($phaseId, $phases, sprintf('Import job "%s" declares no phase "%s"', $reference, $phaseId));

            unset($expectedPhase['id']);
            foreach ($expectedPhase as $property => $expected) {
                Assert::assertArrayHasKey($property, $phases[$phaseId], sprintf('Unknown phase property "%s"', $property));
                Assert::assertSame($expected, $phases[$phaseId][$property], sprintf('Phase "%s" property "%s"', $phaseId, $property));
            }
        }
    }

    /**
     * Asserting the absence matters as much as the presence: a scenario that only ever checks the
     * messages it expects cannot tell a clean batch from a noisy one.
     *
     * @Then import job :reference should report no message
     */
    public function assertNoImportJobMessage(string $reference): void
    {
        $messages = $this->getImportJobState($reference)->getMessages();

        Assert::assertEmpty($messages, sprintf("Import job \"%s\" reports:\n%s", $reference, $this->describeMessages($messages)));
    }

    /**
     * Each table row must match one reported message. "message" matches on a substring, because
     * the wording belongs to the importer and a scenario should not repeat it in full.
     *
     * @Then import job :reference should report the following messages:
     */
    public function assertImportJobMessages(string $reference, TableNode $table): void
    {
        $messages = $this->getImportJobState($reference)->getMessages();

        foreach ($table->getColumnsHash() as $expected) {
            Assert::assertNotEmpty(
                array_filter($messages, fn (ImportJobMessage $message): bool => $this->matchesMessage($message, $expected)),
                sprintf("Import job \"%s\" reports no message matching:\n%s\nReported:\n%s", $reference, json_encode($expected), $this->describeMessages($messages))
            );
        }
    }

    /**
     * @Then the import job :reference should no longer exist
     */
    public function assertImportJobIsGone(string $reference): void
    {
        try {
            $this->getImportJobState($reference);
        } catch (ImportJobNotFoundException) {
            return;
        }

        Assert::fail(sprintf('Import job "%s" still exists', $reference));
    }

    /**
     * @Then I should get an error that the import job cannot be continued
     * @Then I should get an error that the import job cannot be cancelled
     */
    public function assertStatusError(): void
    {
        $this->assertLastErrorIs(ImportJobStatusException::class);
    }

    /**
     * @Then I should get an error that the import job already has a batch in progress
     */
    public function assertAlreadyRunningError(): void
    {
        $this->assertLastErrorIs(ImportJobAlreadyRunningException::class);
    }

    /**
     * @Then I should get an error that the import job does not exist
     */
    public function assertNotFoundError(): void
    {
        $this->assertLastErrorIs(ImportJobNotFoundException::class);
    }

    /**
     * @Then I should get an error that the import job expiration date is invalid
     */
    public function assertInvalidExpirationDateError(): void
    {
        $this->assertLastErrorIs(ImportJobConstraintException::class, ImportJobConstraintException::INVALID_EXPIRATION_DATE);
    }

    /**
     * @Then I should get an error that the import cannot start because :reason
     */
    public function assertCannotStartError(string $reason): void
    {
        $codes = [
            'the shop scope is empty' => CannotStartImportJobException::EMPTY_SHOP_SCOPE,
            'the shop scope is too wide' => CannotStartImportJobException::UNSUPPORTED_SHOP_SCOPE,
            'the entity type is unknown' => CannotStartImportJobException::UNKNOWN_ENTITY_TYPE,
            'the file is empty' => CannotStartImportJobException::EMPTY_SOURCE_FILE,
            'the file is out of bounds' => CannotStartImportJobException::SOURCE_FILE_OUT_OF_BOUNDS,
            'the file was not found' => CannotStartImportJobException::SOURCE_FILE_NOT_FOUND,
            'the language is not installed' => CannotStartImportJobException::UNKNOWN_LANGUAGE,
            'truncating is not supported' => CannotStartImportJobException::UNSUPPORTED_TRUNCATE,
            'a dry run is not supported' => CannotStartImportJobException::UNSUPPORTED_DRY_RUN,
        ];

        Assert::assertArrayHasKey($reason, $codes, 'Unknown reason in the scenario');
        $this->assertLastErrorIs(CannotStartImportJobException::class, $codes[$reason]);
    }

    /**
     * The import directory sits inside the Behat tree, since _PS_ADMIN_DIR_ points there during
     * tests, so nothing may survive a scenario — CI fails on a dirty working tree.
     *
     * @AfterScenario
     */
    public function cleanUpImportFiles(): void
    {
        $filesystem = new Filesystem();

        // a source only survives when Start refused it: the handler deletes it on success
        $leftovers = array_merge(
            glob(sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::SOURCE_PREFIX . '*') ?: [],
            glob($this->getImportDirectory() . 'work' . DIRECTORY_SEPARATOR . '*.csv') ?: []
        );

        foreach ($leftovers as $leftover) {
            $filesystem->remove($leftover);
        }
    }

    /**
     * @return list<string>
     */
    private function findSourceFiles(string $reference): array
    {
        return glob(sys_get_temp_dir() . DIRECTORY_SEPARATOR . self::SOURCE_PREFIX . $reference . '.*') ?: [];
    }

    private function purge(?string $expirationDate): ?ImportJobPurgeSummary
    {
        try {
            return $this->getCommandBus()->handle(new PurgeImportJobsCommand(
                null === $expirationDate ? null : new DateTimeImmutable($expirationDate)
            ));
        } catch (Exception $exception) {
            $this->setLastException($exception);

            return null;
        }
    }

    private function assertPurgeResult(?ImportJobPurgeSummary $summary, TableNode $table): void
    {
        Assert::assertNotNull($summary, 'The purge did not return a result');

        $actual = [
            'purgedJobCount' => (string) $summary->getPurgedJobCount(),
            'removedWorkingFileCount' => (string) $summary->getRemovedWorkingFileCount(),
        ];

        foreach ($table->getRowsHash() as $property => $expected) {
            Assert::assertArrayHasKey($property, $actual, sprintf('Unknown purge result property "%s"', $property));
            Assert::assertSame($expected, $actual[$property], sprintf('Purge result property "%s"', $property));
        }
    }

    /**
     * The header names the fields, which is what the mapping screen ends up producing anyway.
     *
     * @return array<int, string>
     */
    private function readFieldMapping(string $source): array
    {
        $handle = fopen($source, 'r');
        $header = fgetcsv($handle, 0, ';');
        fclose($handle);

        Assert::assertIsArray($header, sprintf('Import fixture "%s" has no header row', $source));

        return array_map('trim', $header);
    }

    /**
     * @param array<string, string> $expected
     */
    private function matchesMessage(ImportJobMessage $message, array $expected): bool
    {
        foreach ($expected as $property => $value) {
            $actual = match ($property) {
                'severity' => $message->getSeverity(),
                'phase' => $message->getPhase(),
                'field' => (string) $message->getField(),
                'rows' => implode(',', $message->getRows()),
                'rowCount' => (string) $message->getRowCount(),
                'message' => $message->getMessage(),
                default => Assert::fail(sprintf('Unknown message property "%s"', $property)),
            };

            if ('message' === $property ? !str_contains($actual, $value) : $actual !== $value) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param list<ImportJobMessage> $messages
     */
    private function describeMessages(array $messages): string
    {
        if ([] === $messages) {
            return '  (none)';
        }

        return implode("\n", array_map(
            static fn (ImportJobMessage $message): string => sprintf(
                '  [%s/%s] %s (rows: %s)',
                $message->getSeverity(),
                $message->getPhase(),
                $message->getMessage(),
                implode(',', $message->getRows())
            ),
            $messages
        ));
    }

    /**
     * @return list<ImportJobMessage>
     */
    private function getMessagesBySeverity(ImportJobState $state, string $severity): array
    {
        return array_values(array_filter(
            $state->getMessages(),
            static fn (ImportJobMessage $message): bool => $message->getSeverity() === $severity
        ));
    }

    private function getImportJobState(string $reference): ImportJobState
    {
        return $this->getQueryBus()->handle(new GetImportJobState($this->referenceToUuid($reference)));
    }

    /**
     * ImportDirectory is a private service, so the scenarios rebuild its two paths rather than
     * make a production service public for the tests. A change to the convention breaks these
     * assertions, which is the outcome we want.
     */
    private function getWorkingFilePath(string $reference): string
    {
        return $this->getImportDirectory() . 'work' . DIRECTORY_SEPARATOR . $this->referenceToUuid($reference) . '.csv';
    }

    private function getImportDirectory(): string
    {
        return _PS_ADMIN_DIR_ . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR;
    }

    /**
     * @param array<string, string> $options
     *
     * @return array<string, mixed>
     */
    private function castOptions(array $options): array
    {
        return array_map(static function (string $value) {
            if (in_array($value, ['true', 'false'], true)) {
                return 'true' === $value;
            }

            return ctype_digit($value) ? (int) $value : $value;
        }, $options);
    }
}
