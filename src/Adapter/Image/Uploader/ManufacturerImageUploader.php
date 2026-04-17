<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Adapter\ServiceLocator;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use PrestaShopException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads manufacturer logo image
 */
final class ManufacturerImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    /**
     * {@inheritdoc}
     */
    public function upload($manufacturerId, UploadedFile $image)
    {
        $this->checkImageIsAllowedForUpload($image);
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

        if (!$temporaryImageName) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        if (!move_uploaded_file($image->getPathname(), $temporaryImageName)) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        // Evaluate the memory required to resize the image: if it's too much, you can't resize it.
        if (!ImageManager::checkImageMemoryLimit($temporaryImageName)) {
            throw new MemoryLimitException('Due to memory limit restrictions, this image cannot be loaded. Increase your memory_limit value.');
        }
        // Copy new image
        if (!ImageManager::resize($temporaryImageName, _PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg')) {
            throw new ImageOptimizationException('An error occurred while uploading the image. Check your directory permissions.');
        }

        $this->generateDifferentSizeImages($manufacturerId);
    }

    /**
     * @param int $manufacturerId
     *
     * @return bool
     */
    private function generateDifferentSizeImages($manufacturerId)
    {
        $resized = true;

        try {
            /* Generate images with different size */
            if (count($_FILES)
                && file_exists(_PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg')
            ) {
                $imageTypes = ImageType::getImagesTypes('manufacturers');
                $configuredImageFormats = ServiceLocator::get(ImageFormatConfiguration::class)->getGenerationFormats();

                foreach ($imageTypes as $imageType) {
                    foreach ($configuredImageFormats as $imageFormat) {
                        $resized &= ImageManager::resize(
                            _PS_MANU_IMG_DIR_ . $manufacturerId . '.jpg',
                            _PS_MANU_IMG_DIR_ . $manufacturerId . '-' . stripslashes($imageType['name']) . '.' . $imageFormat,
                            (int) $imageType['width'],
                            (int) $imageType['height'],
                            $imageFormat
                        );
                    }
                }

                $currentLogo = _PS_TMP_IMG_DIR_ . 'manufacturer_mini_' . $manufacturerId . '.jpg';

                if ($resized && file_exists($currentLogo)) {
                    unlink($currentLogo);
                }
            }
        } catch (PrestaShopException) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        if (!$resized) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        return $resized;
    }
}
