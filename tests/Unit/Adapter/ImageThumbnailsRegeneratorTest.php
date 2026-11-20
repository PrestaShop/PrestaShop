<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\ImageThumbnailsRegenerator;
use PrestaShop\PrestaShop\Adapter\Product\Image\ProductImagePathFactory;
use PrestaShop\PrestaShop\Adapter\Product\Image\Repository\ProductImageRepository;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\QueryResult\ProductImageForThumbnailGeneration;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\ImageType;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageThumbnailsRegeneratorTest extends TestCase
{
    private string $imageDir;

    protected function setUp(): void
    {
        $this->imageDir = sys_get_temp_dir() . '/ps_thumbnails_regenerator_' . uniqid() . '/';
        mkdir($this->imageDir);
    }

    protected function tearDown(): void
    {
        (new Filesystem())->remove($this->imageDir);
    }

    /**
     * @dataProvider workerExitCodesProvider
     */
    public function testWorkerExitCodesAreReported(string $workerCode, array $expectedErrors): void
    {
        $regenerator = $this->createRegenerator($workerCode);
        $regenerator->setWorkers(2);

        $errors = $regenerator->regenerateNewImages($this->imageDir, [$this->createImageType('home'), $this->createImageType('large')], true);

        $this->assertSame($expectedErrors, $errors);
    }

    public static function workerExitCodesProvider(): iterable
    {
        yield 'success' => ['exit(0);', []];
        yield 'timeout' => ['exit(' . ImageThumbnailsRegenerator::WORKER_TIMEOUT_EXIT_CODE . ');', ['timeout']];
        yield 'failure' => ['echo "boom"; exit(1);', ['boom']];
    }

    public function testWorkersDeleteTheirOwnProductThumbnails(): void
    {
        $repository = $this->createMock(ProductImageRepository::class);
        $repository->expects($this->never())->method('iterateImagesForThumbnailGeneration');
        $regenerator = $this->createRegenerator('exit(in_array("--delete", $argv, true) ? 0 : 1);', $repository);
        $regenerator->setWorkers(2);
        $types = [$this->createImageType('home'), $this->createImageType('large')];

        $this->assertTrue($regenerator->deletePreviousImages($this->imageDir, $types, true));
        $this->assertSame([], $regenerator->regenerateNewImages($this->imageDir, $types, true));
    }

    public function testWorkersOnlyDeleteWhenAsked(): void
    {
        $regenerator = $this->createRegenerator('exit(in_array("--delete", $argv, true) ? 1 : 0);');
        $types = [$this->createImageType('home'), $this->createImageType('large')];
        $regenerator->setWorkers(2);
        $regenerator->deletePreviousImages($this->imageDir, $types, true);
        $regenerator->regenerateNewImages($this->imageDir, $types, true);

        $regenerator->setWorkers(2);

        $this->assertSame([], $regenerator->regenerateNewImages($this->imageDir, $types, true));
    }

    public function testWorkerOnlyDeletesThumbnailsOfItsImages(): void
    {
        foreach ([1, 2] as $imageId) {
            mkdir($this->imageDir . $imageId);
            touch($this->imageDir . $imageId . '/' . $imageId . '-home.jpg');
        }
        touch($this->imageDir . 'fr-default-home.jpg');

        $repository = $this->createMock(ProductImageRepository::class);
        $repository->method('iterateImagesForThumbnailGeneration')->willReturnCallback(static function () {
            yield new ProductImageForThumbnailGeneration(new ImageId(1), new ProductId(1));
            yield new ProductImageForThumbnailGeneration(new ImageId(2), new ProductId(1));
        });
        $pathFactory = $this->createMock(ProductImagePathFactory::class);
        $pathFactory->method('getImageFolder')->willReturnCallback(fn (ImageId $imageId) => $this->imageDir . $imageId->getValue());
        $regenerator = $this->createRegenerator('exit(0);', $repository, $pathFactory);
        $regenerator->setWorkers(2, 1);

        $regenerator->deletePreviousImages($this->imageDir, [$this->createImageType('home')], true);

        $this->assertFileDoesNotExist($this->imageDir . '1/1-home.jpg');
        $this->assertFileExists($this->imageDir . '2/2-home.jpg');
        $this->assertFileExists($this->imageDir . 'fr-default-home.jpg');
    }

    public function testWatermarkIsLeftToWorkers(): void
    {
        $regenerator = $this->createRegenerator('exit(0);');
        $regenerator->setWorkers(2);

        $this->assertTrue($regenerator->regenerateWatermark($this->imageDir));
    }

    /**
     * Each worker runs $workerCode with the worker arguments in $argv.
     */
    private function createRegenerator(string $workerCode, ?ProductImageRepository $repository = null, ?ProductImagePathFactory $pathFactory = null): ImageThumbnailsRegenerator
    {
        return new class($repository ?? $this->createMock(ProductImageRepository::class), $this->createMock(ImageFormatConfiguration::class), $this->createMock(LanguageRepositoryInterface::class), $this->createMock(ConfigurationInterface::class), $this->createMock(TranslatorInterface::class), $pathFactory ?? $this->createMock(ProductImagePathFactory::class), $workerCode) extends ImageThumbnailsRegenerator {
            public function __construct(
                ProductImageRepository $productImageRepository,
                ImageFormatConfiguration $imageFormatConfiguration,
                LanguageRepositoryInterface $langRepository,
                ConfigurationInterface $configuration,
                TranslatorInterface $translator,
                ProductImagePathFactory $productImagePathFactory,
                private readonly string $workerCode,
            ) {
                parent::__construct($productImageRepository, $imageFormatConfiguration, $langRepository, $configuration, $translator, $productImagePathFactory);
            }

            protected function getWorkerCommand(): array
            {
                return [PHP_BINARY, '-r', $this->workerCode, '--'];
            }
        };
    }

    private function createImageType(string $name): ImageType
    {
        $imageType = $this->createMock(ImageType::class);
        $imageType->method('getName')->willReturn($name);

        return $imageType;
    }
}
