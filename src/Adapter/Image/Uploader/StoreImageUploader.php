<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class StoreImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    public function upload($storeId, UploadedFile $image): void
    {
        $this->checkImageIsAllowedForUpload($image);
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

        if (!$temporaryImageName) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        if (!move_uploaded_file($image->getPathname(), $temporaryImageName)) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        if (!ImageManager::checkImageMemoryLimit($temporaryImageName)) {
            throw new MemoryLimitException('Due to memory limit restrictions, this image cannot be loaded. Increase your memory_limit value.');
        }

        if (!ImageManager::resize($temporaryImageName, _PS_STORE_IMG_DIR_ . $storeId . '.jpg')) {
            throw new ImageOptimizationException('An error occurred while uploading the image. Check your directory permissions.');
        }

        $this->generateDifferentSizeImages($storeId);
    }

    private function generateDifferentSizeImages(int $storeId): void
    {
        if (!file_exists(_PS_STORE_IMG_DIR_ . $storeId . '.jpg')) {
            return;
        }

        $imageTypes = ImageType::getImagesTypes('stores');
        $configuredImageFormats = ServiceLocator::get(ImageFormatConfiguration::class)->getGenerationFormats();

        foreach ($imageTypes as $imageType) {
            foreach ($configuredImageFormats as $imageFormat) {
                ImageManager::resize(
                    _PS_STORE_IMG_DIR_ . $storeId . '.jpg',
                    _PS_STORE_IMG_DIR_ . $storeId . '-' . stripslashes($imageType['name']) . '.' . $imageFormat,
                    (int) $imageType['width'],
                    (int) $imageType['height'],
                    $imageFormat
                );
            }
        }
    }
}
