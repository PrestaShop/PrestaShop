<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Integration\Behaviour\Features\Context\Domain;

use PHPUnit\Framework\Assert;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\CancelImportRunCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\StartImportRunCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportException;
use PrestaShop\PrestaShop\Core\Domain\Import\Query\GetImportRunState;
use PrestaShop\PrestaShop\Core\Domain\Import\QueryResult\ImportRunState;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\EntityType;
use Tests\Integration\Behaviour\Features\Context\LastExceptionTrait;

/**
 * Exercises the ImportRun aggregate through the command/query bus: the run lifecycle (start →
 * cancel), reading run state and the domain constraints.
 *
 * Note: a full RunImportBatch execution scenario is not covered here — the modern import executor
 * still depends on an HTTP session/web context, so it cannot run under the CQRS Behat harness yet
 * (and only products/categories have a modern handler). Decoupling the executor from the web
 * context is follow-up work.
 */
class ImportRunFeatureContext extends AbstractDomainFeatureContext
{
    use LastExceptionTrait;

    private const DEFAULT_LANG_ISO = 'en';

    /**
     * @var string[] files copied into the import directory, removed after each scenario
     */
    private $importFilesToClean = [];

    /**
     * @When I start an import run :reference for :entityType from file :filename
     */
    public function startSimpleImportRun(string $reference, string $entityType, string $filename): void
    {
        $this->prepareImportFile($filename);

        $importRunId = $this->getCommandBus()->handle(new StartImportRunCommand(
            $filename,
            EntityType::fromName($entityType)->getValue(),
            self::DEFAULT_LANG_ISO,
            [],
            [],
            true
        ));

        $this->getSharedStorage()->set($reference, $importRunId->getValue());
    }

    /**
     * @AfterScenario
     */
    public function cleanUpImportFiles(): void
    {
        foreach ($this->importFilesToClean as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        $this->importFilesToClean = [];
    }

    /**
     * Starting a run now counts the file rows up front, so the referenced file must exist in the
     * import directory. Copies a dummy CSV fixture under the requested name and schedules cleanup.
     */
    private function prepareImportFile(string $filename): void
    {
        $importDir = _PS_ADMIN_DIR_ . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR;
        if (!is_dir($importDir)) {
            mkdir($importDir, 0777, true);
        }

        $destination = $importDir . $filename;
        copy(dirname(__DIR__, 5) . '/Resources/import/dummy.csv', $destination);

        $this->importFilesToClean[] = $destination;
    }

    /**
     * @When I cancel import run :reference
     */
    public function cancelImportRun(string $reference): void
    {
        $this->getCommandBus()->handle(new CancelImportRunCommand($this->getSharedStorage()->get($reference)));
    }

    /**
     * @Then import run :reference should have status :status
     */
    public function assertImportRunStatus(string $reference, string $status): void
    {
        $state = $this->getImportRunState($reference);

        Assert::assertSame(
            $status,
            $state->getStatus(),
            sprintf('Expected import run "%s" to be "%s" but it is "%s".', $reference, $status, $state->getStatus())
        );
    }

    /**
     * @When I start an import run with an empty file name
     */
    public function startImportRunWithEmptyFilename(): void
    {
        try {
            $this->getCommandBus()->handle(new StartImportRunCommand('', EntityType::fromName('categories')->getValue(), self::DEFAULT_LANG_ISO));
        } catch (ImportException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I start an import run for unsupported entity type :type
     */
    public function startImportRunWithUnsupportedType(int $type): void
    {
        try {
            $this->getCommandBus()->handle(new StartImportRunCommand('categories.csv', $type, self::DEFAULT_LANG_ISO));
        } catch (ImportException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I start an import run with a negative batch size
     */
    public function startImportRunWithNegativeBatchSize(): void
    {
        try {
            $this->getCommandBus()->handle(new StartImportRunCommand(
                'categories.csv',
                EntityType::fromName('categories')->getValue(),
                self::DEFAULT_LANG_ISO,
                [],
                [],
                false,
                -5
            ));
        } catch (ImportException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @When I ask for the state of a non-existent import run
     */
    public function getNonExistentImportRunState(): void
    {
        try {
            $this->getQueryBus()->handle(new GetImportRunState('11111111-1111-4111-8111-111111111111'));
        } catch (ImportException $e) {
            $this->setLastException($e);
        }
    }

    /**
     * @Then I should get an import error :exceptionClass
     */
    public function assertImportError(string $exceptionClass): void
    {
        $this->assertLastErrorIs($exceptionClass);
    }

    /**
     * @Then I should get an import error :exceptionClass with code :code
     */
    public function assertImportErrorWithCode(string $exceptionClass, int $code): void
    {
        $this->assertLastErrorIs($exceptionClass, $code);
    }

    private function getImportRunState(string $reference): ImportRunState
    {
        return $this->getQueryBus()->handle(new GetImportRunState($this->getSharedStorage()->get($reference)));
    }
}
