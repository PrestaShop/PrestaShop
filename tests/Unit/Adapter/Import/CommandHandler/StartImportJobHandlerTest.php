<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Import\CommandHandler;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Import\CommandHandler\StartImportJobHandler;
use PrestaShop\PrestaShop\Adapter\Import\ImportTruncator;
use PrestaShop\PrestaShop\Adapter\Import\Job\ImportJobPurger;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\StartImportJobCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\CannotStartImportJobException;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterInterface;
use PrestaShop\PrestaShop\Core\Import\Engine\EntityImporterRegistry;
use PrestaShop\PrestaShop\Core\Import\Engine\File\CsvImportFileNormalizer;
use PrestaShop\PrestaShop\Core\Import\Engine\ImportPhaseDefinition;
use PrestaShop\PrestaShop\Core\Import\Entity\ImportEntityDeleterInterface;
use PrestaShop\PrestaShop\Core\Import\ImportDirectory;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShop\PrestaShop\Core\Shop\ShopListResolverInterface;
use PrestaShopBundle\Entity\Repository\ImportJobRepository;
use Psr\Log\NullLogger;
use Symfony\Component\Filesystem\Filesystem;

/**
 * The guards Behat cannot reach: the scripted importer declares a validation phase.
 */
class StartImportJobHandlerTest extends TestCase
{
    public function testADryRunIsRefusedForAnImporterWithNoValidationPhase(): void
    {
        $this->expectException(CannotStartImportJobException::class);
        $this->expectExceptionCode(CannotStartImportJobException::UNSUPPORTED_DRY_RUN);

        $this->buildHandler([new ImportPhaseDefinition(ImportPhaseDefinition::PHASE_DATABASE, 'Importing')])
            ->handle(new StartImportJobCommand(
                '/tmp/notes.csv',
                'demo_note',
                'en',
                ShopConstraint::shop(1),
                ['note'],
                ['dryRun' => true]
            ));
    }

    /**
     * @param list<ImportPhaseDefinition> $phases
     */
    private function buildHandler(array $phases): StartImportJobHandler
    {
        $importer = $this->createMock(EntityImporterInterface::class);
        $importer->method('getEntityType')->willReturn('demo_note');
        $importer->method('getPhases')->willReturn($phases);

        $shopListResolver = $this->createMock(ShopListResolverInterface::class);
        $shopListResolver->method('resolveShopIds')->willReturn([1]);

        $repository = $this->createMock(ImportJobRepository::class);
        $importDirectory = new ImportDirectory($this->createMock(ConfigurationInterface::class), sys_get_temp_dir());
        $filesystem = $this->createMock(Filesystem::class);

        return new StartImportJobHandler(
            new EntityImporterRegistry([$importer]),
            $repository,
            $shopListResolver,
            $this->createMock(LanguageRepositoryInterface::class),
            new ImportTruncator($this->createMock(ImportEntityDeleterInterface::class)),
            $importDirectory,
            $this->createMock(CsvImportFileNormalizer::class),
            new ImportJobPurger($repository, $importDirectory, $filesystem),
            $filesystem,
            new NullLogger()
        );
    }
}
