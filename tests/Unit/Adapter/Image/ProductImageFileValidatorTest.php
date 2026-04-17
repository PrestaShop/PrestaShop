<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Tests\Unit\Adapter\Image;

use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Image\ProductImageFileValidator;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageSizeException;
use Tests\Resources\DummyFileUploader;
use Throwable;

class ProductImageFileValidatorTest extends TestCase
{
    /**
     * @dataProvider getInvalidMaxUploadSizesForFile
     *
     * @param string $filePath
     * @param int $maxPhpIniUploadSize
     * @param int $maxUploadQuotaSize
     *
     * @throws UploadedImageConstraintException
     * @throws ImageUploadException
     */
    public function testItThrowsExceptionWhenFileSizeIsLargerThanMaxUploadSize(
        string $filePath,
        int $maxPhpIniUploadSize,
        int $maxUploadQuotaSize
    ) {
        $imageValidator = new ProductImageFileValidator($maxPhpIniUploadSize, $this->mockQuotaConfiguration($maxUploadQuotaSize));

        $this->expectException(UploadedImageSizeException::class);

        $imageValidator->assertFileUploadLimits($filePath);
    }

    /**
     * @dataProvider getValidMaxUploadSizesForFile
     *
     * @param string $filePath
     * @param int $maxPhpIniUploadSize
     * @param int $maxUploadQuotaSize
     */
    public function testItValidatesImageFileSuccessfully(
        string $filePath,
        int $maxPhpIniUploadSize,
        int $maxUploadQuotaSize
    ): void {
        $imageValidator = new ProductImageFileValidator(
            $maxPhpIniUploadSize,
            $this->mockQuotaConfiguration($maxUploadQuotaSize)
        );

        $exception = null;

        try {
            $imageValidator->assertFileUploadLimits($filePath);
        } catch (Throwable $e) {
            $exception = $e;
        }

        // assert that no exception was thrown, so that phpunit doesn't complain about missing assertions
        $this->assertNull($exception);
    }

    public function getValidMaxUploadSizesForFile(): iterable
    {
        // logo size is 2.76kb
        $logoPath = DummyFileUploader::getDummyFilesPath() . 'logo.jpg';
        // app_icon size is 19.19kb
        $appIconPath = DummyFileUploader::getDummyFilesPath() . 'app_icon.png';

        yield [$logoPath, 2770, 2770];
        yield [$logoPath, 2770, 3100];
        yield [$appIconPath, 20000, 20000];
        yield [$appIconPath, 21000, 20000];
    }

    public function getInvalidMaxUploadSizesForFile(): iterable
    {
        $logoPath = DummyFileUploader::getDummyFilesPath() . 'logo.jpg';
        $appIconPath = DummyFileUploader::getDummyFilesPath() . 'app_icon.png';

        yield [$logoPath, 2750, 2750];
        yield [$appIconPath, 100, 100];
        // when value from quota configuration is lower, then it should be checked instead of php.ini config value
        yield [$appIconPath, 5000000, 100];
        // when value from php.ini configuration is lower, then quota config value should be ignored
        yield [$appIconPath, 100, 5000000];
    }

    private function mockQuotaConfiguration(int $maxSizeInBytes): DataConfigurationInterface
    {
        $configuration = $this->createMock(DataConfigurationInterface::class);
        $configuration
            ->method('getConfiguration')
            ->willReturn([
                // set size in megabytes
                'max_size_product_image' => $maxSizeInBytes / 1000000,
            ]);

        return $configuration;
    }
}
