<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image;

use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShopException;

/**
 * Responsible for resizing images based on provided types
 */
class ImageGenerator
{
    public function __construct(
        private readonly ImageFormatConfiguration $imageFormatConfiguration
    ) {
    }

    /**
     * @param string $imagePath
     * @param ImageType[] $imageTypes
     * @param int $imageId
     *
     * @return bool
     *
     * @throws ImageOptimizationException
     * @throws ImageUploadException
     */
    public function generateImagesByTypes(string $imagePath, array $imageTypes, int $imageId = 0): bool
    {
        $resized = true;

        try {
            foreach ($imageTypes as $imageType) {
                $resized &= $this->resize($imagePath, $imageType, $imageId);
            }
        } catch (PrestaShopException) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        if (!$resized) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        return (bool) $resized;
    }

    /**
     * Resizes the image depending on its type
     *
     * @param string $filePath
     * @param ImageType $imageType
     * @param int $imageId
     *
     * @return bool
     */
    protected function resize(string $filePath, ImageType $imageType, int $imageId = 0): bool
    {
        if (!is_file($filePath)) {
            throw new ImageUploadException(sprintf('File "%s" does not exist', $filePath));
        }

        /*
         * Let's resolve which formats we will use for image generation.
         *
         * In case of .jpg images, the actual format inside is decided by ImageManager.
         */
        $configuredImageFormats = $this->imageFormatConfiguration->getGenerationFormats();

        $result = true;

        foreach ($configuredImageFormats as $imageFormat) {
            if (!ImageManager::resize(
                $filePath,
                sprintf('%s-%s.%s', dirname($filePath) . DIRECTORY_SEPARATOR . $imageId, stripslashes($imageType->name), $imageFormat),
                $imageType->width,
                $imageType->height,
                $imageFormat
            )) {
                $result = false;
            }
        }

        return $result;
    }
}
