<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
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
