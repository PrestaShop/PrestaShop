<?php
/**
 * 2007-2018 PrestaShop
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use Category;
use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Tools;

/**
 * Class CategoryCoverImageUploader
 *
 * @internal
 */
final class CategoryCoverImageUploader implements ImageUploaderInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws MemoryLimitException
     * @throws ImageOptimizationException
     * @throws ImageUploadException
     * @throws UploadedImageConstraintException
     */
    public function upload($id, UploadedFile $uploadedImage)
    {
        $this->deleteOldImage($id);
        $this->checkImageIsAllowedForUpload($uploadedImage);
        $this->uploadImage($uploadedImage, $id);
        $this->generateDifferentTypes($id);
    }

    /**
     * Delete old category image
     *
     * @param int $id
     */
    private function deleteOldImage($id)
    {
        $category = new Category($id);
        $category->deleteImage();
    }

    /**
     * @param UploadedFile $image
     * @param int $id
     *
     * @return string
     *
     * @throws ImageOptimizationException
     * @throws ImageUploadException
     * @throws MemoryLimitException
     */
    private function uploadImage(UploadedFile $image, $id)
    {
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

        if (!$temporaryImageName) {
            throw new ImageUploadException('Failed to create temporary image file');
        }

        if (!move_uploaded_file($image->getPathname(), $temporaryImageName)) {
            throw new ImageUploadException('Failed to upload image');
        }

        if (!ImageManager::checkImageMemoryLimit($temporaryImageName)) {
            throw new MemoryLimitException('Cannot upload image due to memory restrictions');
        }

        $optimizationSucceeded = ImageManager::resize(
            $temporaryImageName,
            _PS_IMG_DIR_ . 'c'. DIRECTORY_SEPARATOR . $id . '.jpg',
            null,
            null,
            'jpg'
        );

        if (!$optimizationSucceeded) {
            throw new ImageOptimizationException('Failed to optimize image after uploading');
        }

        unlink($temporaryImageName);
    }

    /**
     * @param int $id
     *
     * @throws ImageUploadException
     */
    private function generateDifferentTypes($id)
    {
        if (!file_exists(_PS_CAT_IMG_DIR_ . $id . '.jpg')) {
            return;
        }

        $imagesTypes = ImageType::getImagesTypes('categories');
        foreach ($imagesTypes as $k => $imageType) {
            $generated = ImageManager::resize(
                _PS_CAT_IMG_DIR_ . $id . '.jpg',
                _PS_CAT_IMG_DIR_ . $id . '-' . stripslashes($imageType['name']) . '.jpg',
                (int) $imageType['width'],
                (int) $imageType['height']
            );

            if (!$generated) {
                throw new ImageUploadException('Error occurred when uploading image');
            }
        }
    }

    /**
     * Check if image is allowed to be uploaded.
     *
     * @param UploadedFile $image
     *
     * @throws UploadedImageConstraintException
     */
    private function checkImageIsAllowedForUpload(UploadedFile $image)
    {
        $maxFileSize = Tools::getMaxUploadSize();

        if ($maxFileSize > 0 && $image->getSize() > $maxFileSize) {
            throw new UploadedImageConstraintException(
                sprintf(
                    'Max file size allowed is "%s" bytes. Uploaded image size is "%s".',
                    $maxFileSize,
                    $image->getSize()
                ),
                UploadedImageConstraintException::EXCEEDED_SIZE
            );
        }

        if (!ImageManager::isRealImage($image->getPathname(), $image->getClientOriginalExtension())
            || !ImageManager::isCorrectImageFileExt($image->getClientOriginalExtension())
            || preg_match('/\%00/', $image->getClientOriginalName())
        ) {
            throw new UploadedImageConstraintException(
                sprintf(
                    'Image format "%s", not recognized, allowed formats are: .gif, .jpg, .png',
                    $image->getClientOriginalExtension()
                ),
                UploadedImageConstraintException::UNRECOGNIZED_FORMAT
            );
        }
    }
}
