<?php
/**
 * 2007-2020 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2020 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use Configuration;
use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShopException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tools;

/**
 * Class AbstractImageUploader encapsulates reusable legacy methods used for image uploading.
 *
 * @internal
 */
abstract class AbstractImageUploader
{
    /**
     * @deprecated
     * @see assertImageIsAllowedForUpload()
     *
     * @param UploadedFile $image
     *
     * @throws UploadedImageConstraintException
     */
    protected function checkImageIsAllowedForUpload(UploadedFile $image)
    {
        $this->assertImageIsAllowedForUpload($image->getPathname());
    }

    /**
     * @param string $filePath
     *
     * @throws ImageUploadException
     * @throws UploadedImageConstraintException
     */
    protected function assertImageIsAllowedForUpload(string $filePath): void
    {
        if (!is_file($filePath)) {
            throw new ImageUploadException(sprintf('"%s" is not a file', $filePath));
        }

        $maxFileSize = Tools::getMaxUploadSize();

        if ($maxFileSize > 0 && filesize($filePath) > $maxFileSize) {
            throw new UploadedImageConstraintException(sprintf('Max file size allowed is "%s" bytes. Uploaded image size is "%s".', $maxFileSize, $image->getSize()), UploadedImageConstraintException::EXCEEDED_SIZE);
        }

        if (!ImageManager::isRealImage($filePath, mime_content_type($filePath))) {
            throw new UploadedImageConstraintException(sprintf('Image format "%s", not recognized, allowed formats are: .gif, .jpg, .png', $image->getClientOriginalExtension()), UploadedImageConstraintException::UNRECOGNIZED_FORMAT);
        }
    }

    /**
     * Creates temporary image from uploaded file
     *
     * @param UploadedFile $image
     *
     * @throws ImageUploadException
     *
     * @return string
     */
    protected function createTemporaryImage(UploadedFile $image)
    {
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

        if (!$temporaryImageName || !move_uploaded_file($image->getPathname(), $temporaryImageName)) {
            throw new ImageUploadException('Failed to create temporary image file');
        }

        return $temporaryImageName;
    }

    /**
     * Uploads resized image from temporary folder to image destination
     *
     * @param string $temporaryImageName
     * @param string $destination
     *
     * @throws ImageOptimizationException
     * @throws MemoryLimitException
     */
    protected function uploadFromTemp($temporaryImageName, $destination)
    {
        if (!ImageManager::checkImageMemoryLimit($temporaryImageName)) {
            throw new MemoryLimitException('Cannot upload image due to memory restrictions');
        }

        if (!ImageManager::resize($temporaryImageName, $destination)) {
            throw new ImageOptimizationException('An error occurred while uploading the image. Check your directory permissions.');
        }

        unlink($temporaryImageName);
    }

    /**
     * @deprecated use AbstractImageUploader::generateDifferentSizeImages() instead
     */
    protected function generateDifferentSize($id, $imageDir, $belongsTo, string $extension = 'jpg')
    {
        return $this->generateDifferentSizeImages(
            $imageDir . $id,
            $belongsTo,
            $extension
        );
    }

    /**
     * @param string $path
     * @param string $belongsTo to whom the image belongs (for example 'suppliers' or 'categories')
     * @param string $extension
     *
     * @return bool
     *
     * @throws ImageOptimizationException
     */
    protected function generateDifferentSizeImages(string $path, string $belongsTo, string $extension = 'jpg'): bool
    {
        $resized = true;

        try {
            $imageTypes = ImageType::getImagesTypes($belongsTo);

            foreach ($imageTypes as $imageType) {
                $resized &= $this->resize($path, $imageType, $extension);
            }
        } catch (PrestaShopException $e) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        if (!$resized) {
            throw new ImageOptimizationException('Unable to resize one or more of your pictures.');
        }

        return $resized;
    }

    /**
     * Resizes the image depending from its type
     *
     * @param string $path
     * @param array $imageType
     * @param string $extension
     *
     * @return bool
     */
    private function resize(string $path, array $imageType, string $extension)
    {
        $width = $imageType['width'];
        $height = $imageType['height'];

        if (Configuration::get('PS_HIGHT_DPI')) {
            $extension = '2x.' . $extension;
            $width *= 2;
            $height *= 2;
        }

        return ImageManager::resize(
            $path . '.' . $extension,
            $path . '-' . stripslashes($imageType['name']) . '.' . $extension,
            (int) $width,
            (int) $height,
            $extension
        );
    }
}
