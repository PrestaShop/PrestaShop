<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\Import\Command\PurgeImportJobsCommand;
use PrestaShop\PrestaShop\Core\Domain\Import\Exception\ImportJobNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Import\ValueObject\ImportJobPurgeSummary;
use PrestaShopBundle\Command\ImportJobPurgeCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * The command is a wrapper, so what is worth testing is the translation of CLI input into the
 * CQRS command and of the result back into output.
 */
class ImportJobPurgeCommandTest extends TestCase
{
    public function testItPurgesWithTheDefaultWindowAndReportsBothCounts(): void
    {
        $dispatched = null;
        $tester = $this->buildTester(
            new ImportJobPurgeSummary(3, 2),
            function (PurgeImportJobsCommand $command) use (&$dispatched): void { $dispatched = $command; }
        );

        $this->assertSame(Command::SUCCESS, $tester->execute([]));
        $this->assertNotNull($dispatched);
        $this->assertNull($dispatched->getExpirationDate(), 'No --days means the retention window decides');
        $this->assertStringContainsString('3 import job(s) deleted, 2 working file(s) removed.', $tester->getDisplay());
    }

    public function testDaysBecomesAnExpirationDateInThePast(): void
    {
        $dispatched = null;
        $tester = $this->buildTester(
            new ImportJobPurgeSummary(0, 0),
            function (PurgeImportJobsCommand $command) use (&$dispatched): void { $dispatched = $command; }
        );

        $tester->execute(['--days' => '30']);

        $this->assertNotNull($dispatched->getExpirationDate());
        $this->assertLessThan(time(), $dispatched->getExpirationDate()->getTimestamp());
        $this->assertGreaterThan(time() - 31 * 86400, $dispatched->getExpirationDate()->getTimestamp());
    }

    public function testANonNumericWindowIsRefusedBeforeAnythingIsDispatched(): void
    {
        $dispatched = false;
        $tester = $this->buildTester(
            new ImportJobPurgeSummary(0, 0),
            function () use (&$dispatched): void { $dispatched = true; }
        );

        $this->assertSame(Command::INVALID, $tester->execute(['--days' => 'lots']));
        $this->assertFalse($dispatched, 'Nothing may be deleted on a typo');
        $this->assertStringContainsString('positive number of days', $tester->getDisplay());
    }

    public function testADomainFailureIsReportedInsteadOfCrashing(): void
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willThrowException(new ImportJobNotFoundException('Nothing to purge here.'));

        $tester = new CommandTester(new ImportJobPurgeCommand($commandBus));

        $this->assertSame(Command::FAILURE, $tester->execute([]));
        $this->assertStringContainsString('Nothing to purge here.', $tester->getDisplay());
    }

    private function buildTester(ImportJobPurgeSummary $summary, callable $spy): CommandTester
    {
        $commandBus = $this->createMock(CommandBusInterface::class);
        $commandBus->method('handle')->willReturnCallback(
            static function (object $command) use ($summary, $spy): ImportJobPurgeSummary {
                $spy($command);

                return $summary;
            }
        );

        return new CommandTester(new ImportJobPurgeCommand($commandBus));
    }
}
