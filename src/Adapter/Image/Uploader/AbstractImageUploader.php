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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use Configuration;
use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
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
     * @param UploadedFile $image
     *
     * @throws UploadedImageConstraintException
     */
    public function checkImageIsAllowedForUpload(UploadedFile $image)
    {
        $maxFileSize = Tools::getMaxUploadSize();

        if ($maxFileSize > 0 && $image->getSize() > $maxFileSize) {
            throw new UploadedImageConstraintException(sprintf('Max file size allowed is "%s" bytes. Uploaded image size is "%s".', $maxFileSize, $image->getSize()), UploadedImageConstraintException::EXCEEDED_SIZE);
        }

        if (!ImageManager::isRealImage($image->getPathname(), $image->getClientMimeType())
            || !ImageManager::isCorrectImageFileExt($image->getClientOriginalName())
            || preg_match('/\%00/', $image->getClientOriginalName()) // prevent null byte injection
        ) {
            throw new UploadedImageConstraintException(
                sprintf(
                    'Image format "%s", not recognized, allowed formats are: %s',
                    $image->getClientOriginalExtension(),
                    join(', ', ImageManager::EXTENSIONS_SUPPORTED)
                ),
                UploadedImageConstraintException::UNRECOGNIZED_FORMAT
            );
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
     * Generates different size images
     *
     * @param int $id
     * @param string $imageDir
     * @param string $belongsTo to whom the image belongs (for example 'suppliers' or 'categories')
     *
     * @return bool
     *
     * @throws ImageOptimizationException
     */
    protected function generateDifferentSize($id, $imageDir, $belongsTo)
    {
        $resized = true;

        try {
            $imageTypes = ImageType::getImagesTypes($belongsTo);

            foreach ($imageTypes as $imageType) {
                $resized &= $this->resize($id, $imageDir, $imageType);
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
     * Resizes the image depending on its type
     *
     * @param int $id
     * @param string $imageDir
     * @param array $imageType
     *
     * @return bool
     */
    private function resize($id, $imageDir, array $imageType)
    {
        $ext = '.jpg';
        $width = $imageType['width'];
        $height = $imageType['height'];

        if (!ImageManager::resize(
            $imageDir . $id . '.jpg',
            $imageDir . $id . '-' . stripslashes($imageType['name']) . $ext,
            (int) $width,
            (int) $height
        )) {
            return false;
        }

        if ((bool) Configuration::get('PS_HIGHT_DPI') && !ImageManager::resize(
            $imageDir . $id . '.jpg',
            $imageDir . $id . '-' . stripslashes($imageType['name']) . '2x' . $ext,
            (int) $width * 2,
            (int) $height * 2
        )) {
            return false;
        }

        return true;
    }
}
