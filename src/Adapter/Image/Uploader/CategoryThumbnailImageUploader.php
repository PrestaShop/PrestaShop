<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use ImageManager;
use ImageType;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\ImageFormatConfiguration;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class CategoryThumbnailImageUploader.
 */
final class CategoryThumbnailImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    public function __construct(
        private readonly ImageFormatConfiguration $imageFormatConfiguration,
    ) {
    }

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
        $this->checkImageIsAllowedForUpload($uploadedImage);
        $this->deleteOldImage($id);
        $this->uploadImage($id, $uploadedImage);
        $this->generateDifferentTypes($id);
    }

    /**
     * Delete old category image.
     *
     * @param int $id
     */
    private function deleteOldImage($id)
    {
        if (file_exists(_PS_CAT_IMG_DIR_ . $id . '_thumb.jpg')) {
            unlink(_PS_CAT_IMG_DIR_ . $id . '_thumb.jpg');
        }
    }

    /**
     * @param int $id
     * @param UploadedFile $image
     *
     * @throws ImageOptimizationException
     * @throws ImageUploadException
     * @throws MemoryLimitException
     */
    private function uploadImage($id, UploadedFile $image)
    {
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');
        if (!$temporaryImageName) {
            throw new ImageUploadException('Failed to create temporary category thumbnail image file');
        }
        // move_uploaded_file -  also checks that the given file is a file that was uploaded via the POST,
        // this prevents for example that a local file is moved
        if (!move_uploaded_file($image->getPathname(), $temporaryImageName)) {
            throw new ImageUploadException('Failed to upload category thumbnail image');
        }

        if (!ImageManager::checkImageMemoryLimit($temporaryImageName)) {
            throw new MemoryLimitException('Cannot upload category thumbnail image due to memory restrictions');
        }

        $optimizationSucceeded = ImageManager::resize(
            $temporaryImageName,
            _PS_IMG_DIR_ . 'c' . DIRECTORY_SEPARATOR . $id . '_thumb.jpg',
            null,
            null
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
        if (!file_exists(_PS_CAT_IMG_DIR_ . $id . '_thumb.jpg')) {
            return;
        }

        $configuredImageFormats = $this->imageFormatConfiguration->getGenerationFormats();
        $imagesTypes = ImageType::getImagesTypes('categories');
        foreach ($imagesTypes as $imageType) {
            foreach ($configuredImageFormats as $imageFormat) {
                $generated = ImageManager::resize(
                    _PS_CAT_IMG_DIR_ . $id . '_thumb.jpg',
                    _PS_CAT_IMG_DIR_ . $id . '_thumb-' . stripslashes($imageType['name']) . '.' . $imageFormat,
                    (int) $imageType['width'],
                    (int) $imageType['height'],
                    $imageFormat
                );

                if (!$generated) {
                    throw new ImageUploadException('Error occurred when uploading category thumbnail image');
                }
            }
        }
    }
}
