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
use PrestaShop\PrestaShop\Core\CommandBus\CommandBusInterface;
use PrestaShop\PrestaShop\Core\Domain\ImageSettings\Command\RegenerateThumbnailsCommand as DomainRegenerateThumbnailsCommand;
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
