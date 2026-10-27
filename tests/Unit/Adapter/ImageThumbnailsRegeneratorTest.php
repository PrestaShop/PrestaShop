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
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Language\LanguageRepositoryInterface;
use PrestaShopBundle\Entity\ImageType;
use Symfony\Contracts\Translation\TranslatorInterface;

class ImageThumbnailsRegeneratorTest extends TestCase
{
    /**
     * @dataProvider workerExitCodesProvider
     */
    public function testWorkerExitCodesAreReported(string $workerCode, array $expectedErrors): void
    {
        $regenerator = $this->createRegenerator($workerCode);
        $regenerator->setWorkers(2);

        $errors = $regenerator->regenerateNewImages(
            sys_get_temp_dir(),
            [$this->createMock(ImageType::class), $this->createMock(ImageType::class)],
            true
        );

        $this->assertSame($expectedErrors, $errors);
    }

    public static function workerExitCodesProvider(): iterable
    {
        yield 'success' => ['exit(0);', []];
        yield 'timeout' => ['exit(' . ImageThumbnailsRegenerator::WORKER_TIMEOUT_EXIT_CODE . ');', ['timeout']];
        yield 'failure' => ['echo "boom"; exit(1);', ['boom']];
    }

    /**
     * Each worker runs $workerCode with the worker arguments in $argv.
     */
    private function createRegenerator(string $workerCode): ImageThumbnailsRegenerator
    {
        return new class($this->createMock(ProductImageRepository::class), $this->createMock(ImageFormatConfiguration::class), $this->createMock(LanguageRepositoryInterface::class), $this->createMock(ConfigurationInterface::class), $this->createMock(TranslatorInterface::class), $this->createMock(ProductImagePathFactory::class), $workerCode) extends ImageThumbnailsRegenerator {
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
}
