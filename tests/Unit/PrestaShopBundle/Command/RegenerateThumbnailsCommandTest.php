<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\PrestaShopBundle\Command;

use Generator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ImageThumbnailsRegenerator;
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\RegenerateThumbnailsCommand as DomainRegenerateThumbnailsCommand;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Exception\RegenerateThumbnailsTimeoutException;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\ImageDomain;
use PrestaShopBundle\Command\RegenerateThumbnailsCommand;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

class RegenerateThumbnailsCommandTest extends TestCase
{
    private CommandBusInterface&MockObject $commandBus;
    private RegenerateThumbnailsCommand $command;
    private CommandTester $tester;

    protected function setUp(): void
    {
        $this->commandBus = $this->createMock(CommandBusInterface::class);
        $this->command = new RegenerateThumbnailsCommand($this->commandBus);
        $this->tester = new CommandTester($this->command);
    }

    public function testItDispatchesDomainCommand(): void
    {
        $imageType = 5;
        $this->commandBus->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(function (DomainRegenerateThumbnailsCommand $command) use ($imageType) {
                    return $command->getImage() === ImageDomain::PRODUCTS->value
                        && $command->getImageTypeId() === $imageType
                        && $command->erasePreviousImages() === true;
                }));

        $exitCode = $this->tester->execute([
            'image' => 'products',
            'image-type' => $imageType,
            '--delete' => true,
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function testItDispatchesDomainCommandWithShortcutErase(): void
    {
        $imageType = 5;
        $this->commandBus->expects($this->once())
            ->method('handle')
            ->with(
                $this->callback(function (DomainRegenerateThumbnailsCommand $command) use ($imageType) {
                    return $command->getImage() === ImageDomain::PRODUCTS->value
                        && $command->getImageTypeId() === $imageType
                        && $command->erasePreviousImages() === true;
                }));

        $exitCode = $this->tester->execute([
            'image' => 'products',
            'image-type' => $imageType,
            '-d' => true,
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    /**
     * @dataProvider validImageDomainsProvider
     */
    public function testAllValidImageDomains(ImageDomain $imageCategory): void
    {
        $this->commandBus->expects($this->once())->method('handle');

        $exitCode = $this->tester->execute([
            'image' => $imageCategory->value,
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    public function testInvalidImageDomain(): void
    {
        $this->commandBus->expects($this->never())->method('handle');

        $exitCode = $this->tester->execute([
            'image' => 'invalid',
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
    }

    /**
     * @dataProvider invalidImageDomainsProvider
     */
    public function testInvalidImageTypes($imageType): void
    {
        $this->commandBus->expects($this->never())->method('handle');

        $exitCode = $this->tester->execute([
            'image' => 'products',
            'image-type' => $imageType,
        ]);

        $this->assertSame(Command::FAILURE, $exitCode);
    }

    public function testWithoutEraseOption(): void
    {
        $this->commandBus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (DomainRegenerateThumbnailsCommand $command) {
                return $command->erasePreviousImages() === false;
            }));

        $exitCode = $this->tester->execute([
            'image' => 'products',
        ]);

        $this->assertSame(Command::SUCCESS, $exitCode);
    }

    /**
     * @dataProvider invalidImageCasingProvider
     */
    public function testInvalidCasingOrWhitespaceForImage(string $image): void
    {
        $this->commandBus->expects($this->never())->method('handle');

        $exitCode = $this->tester->execute(['image' => $image]);

        $this->assertSame(Command::FAILURE, $exitCode);
    }

    public function testBusFailureIsReported(): void
    {
        $this->commandBus->method('handle')->willThrowException(new RuntimeException('boom'));

        $exitCode = $this->tester->execute(['image' => 'products']);
        $this->assertSame(Command::FAILURE, $exitCode);
    }

    /**
     * @dataProvider workerOptionsProvider
     */
    public function testWorkerOptionsAreDispatched(array $options, int $expectedWorkerCount, ?int $expectedWorkerIndex): void
    {
        $this->commandBus->expects($this->once())
            ->method('handle')
            ->with($this->callback(function (DomainRegenerateThumbnailsCommand $command) use ($expectedWorkerCount, $expectedWorkerIndex) {
                return $command->getWorkerCount() === $expectedWorkerCount && $command->getWorkerIndex() === $expectedWorkerIndex;
            }));

        $this->assertSame(Command::SUCCESS, $this->tester->execute(['image' => 'products', ...$options]));
    }

    public static function workerOptionsProvider(): iterable
    {
        yield 'sequential' => [[], 1, null];
        yield 'parallel' => [['--workers' => '4'], 4, null];
        yield 'one worker' => [['--worker' => '1/4'], 4, 1];
    }

    /**
     * @dataProvider invalidWorkerProvider
     */
    public function testInvalidWorker(string $worker): void
    {
        $this->commandBus->expects($this->never())->method('handle');

        $this->assertSame(Command::FAILURE, $this->tester->execute(['image' => 'products', '--worker' => $worker]));
    }

    public static function invalidWorkerProvider(): iterable
    {
        yield 'not a fraction' => ['abc'];
        yield 'no worker' => ['0/0'];
        yield 'single worker' => ['0/1'];
        yield 'index out of range' => ['2/2'];
    }

    public function testTimeoutExitCode(): void
    {
        $this->commandBus->method('handle')->willThrowException(new RegenerateThumbnailsTimeoutException('timeout'));

        $this->assertSame(Command::FAILURE, $this->tester->execute(['image' => 'products']));
    }

    public function testWorkerTimeoutExitCode(): void
    {
        $this->commandBus->method('handle')->willThrowException(new RegenerateThumbnailsTimeoutException('timeout'));

        $exitCode = $this->tester->execute(['image' => 'products', '--worker' => '1/2']);

        $this->assertSame(ImageThumbnailsRegenerator::WORKER_TIMEOUT_EXIT_CODE, $exitCode);
    }

    public static function numericStringImageTypesProvider(): array
    {
        return [
            ['5', 5],
            ['05', 5],
            ['000', 0],
        ];
    }

    public static function validImageDomainsProvider(): Generator
    {
        yield from array_map(static fn (ImageDomain $imageCategory) => [$imageCategory->name => $imageCategory], ImageDomain::cases());
    }

    public static function invalidImageDomainsProvider(): array
    {
        return [
            'non-numeric string' => ['foo'],
            'negative integer' => [-1],
            'empty string' => [''],
        ];
    }

    public static function invalidImageCasingProvider(): array
    {
        return [
            ['Products'],
            [' products '],
        ];
    }
}
