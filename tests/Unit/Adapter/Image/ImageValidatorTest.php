<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Tests\Unit\Adapter\Image;

use Generator;
use PHPUnit\Framework\TestCase;
use PrestaShop\PrestaShop\Adapter\Image\ImageValidator;
use PrestaShop\PrestaShop\Core\Configuration\IniConfiguration;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageFileNotFoundException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use Tests\Resources\DummyFileUploader;

class ImageValidatorTest extends TestCase
{
    /**
     * @var ImageValidator
     */
    private $imageValidator;

    public function setUp(): void
    {
        require_once __DIR__ . '/../../bootstrap.php';
        $iniConfiguration = new IniConfiguration();
        $this->imageValidator = new ImageValidator($iniConfiguration->getUploadMaxSizeInBytes());
    }

    /**
     * @dataProvider getInvalidMaxUploadSizesForFile
     *
     * @param string $filePath
     * @param int $maxUploadSize
     */
    public function testItThrowsExceptionWhenFileSizeIsLargerThanMaxUploadSize(string $filePath, int $maxUploadSize)
    {
        $imageValidator = new ImageValidator($maxUploadSize);
        $this->expectException(UploadedImageConstraintException::class);
        $this->expectExceptionCode(UploadedImageConstraintException::EXCEEDED_SIZE);

        $imageValidator->assertFileUploadLimits($filePath);
    }

    /**
     * @dataProvider getUnsupportedImageTypes
     *
     * @param string $filePath
     * @param array|null $allowedTypes
     *
     * @throws UploadedImageConstraintException
     * @throws ImageUploadException
     */
    public function testItThrowsExceptionWhenUnsupportedImageTypeIsProvided(string $filePath, ?array $allowedTypes): void
    {
        $this->expectException(UploadedImageConstraintException::class);
        $this->expectExceptionCode(UploadedImageConstraintException::UNRECOGNIZED_FORMAT);

        $this->imageValidator->assertIsValidImageType($filePath, $allowedTypes);
    }

    /**
     * @dataProvider getInvalidPathsToAFile
     *
     * @param string $filePath
     */
    public function testItThrowsExceptionWhenFileDoesNotExistByProvidedPath(string $filePath): void
    {
        $this->expectException(ImageFileNotFoundException::class);
        $this->imageValidator->assertIsValidImageType($filePath);
    }

    public function getInvalidMaxUploadSizesForFile(): Generator
    {
        $logoPath = DummyFileUploader::getDummyFilesPath() . 'logo.jpg';
        $appIconPath = DummyFileUploader::getDummyFilesPath() . 'app_icon.png';

        yield [$logoPath, 2500];
        yield [$logoPath, 2750];
        yield [$appIconPath, 1900];
        yield [$appIconPath, 100];
    }

    public function getUnsupportedImageTypes(): Generator
    {
        // mime type of logo.jpg is "image/jpeg" (not image/jpg) that is why logo.jpg should not be allowed in following case
        yield [DummyFileUploader::getDummyFilesPath() . 'logo.jpg', ['image/jpg', 'image/png', 'image/gif']];
        yield [DummyFileUploader::getDummyFilesPath() . 'app_icon.png', ['image/jpg', 'image/gif']];
        yield [DummyFileUploader::getDummyFilesPath() . 'test_text_file.txt', null];
    }

    public function getInvalidPathsToAFile(): Generator
    {
        yield ['its/definately/notafile', __DIR__];
    }
}
