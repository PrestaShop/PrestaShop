<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use Gender;
use ImageManager;
use PrestaShop\PrestaShop\Core\Domain\Title\Exception\TitleImageUploadingException;
use PrestaShop\PrestaShop\Core\Image\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\UploadedImageConstraintException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Uploads title logo image
 */
class TitleImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    /**
     * {@inheritdoc}
     *
     * @param int|null $imageWidth
     * @param int|null $imageHeight
     *
     * @throws ImageUploadException
     * @throws TitleImageUploadingException
     * @throws UploadedImageConstraintException
     * @throws ImageOptimizationException
     * @throws MemoryLimitException
     */
    public function upload($entityId, UploadedFile $uploadedImage, ?int $imageWidth = null, ?int $imageHeight = null)
    {
        $this->checkImageIsAllowedForUpload($uploadedImage);
        $tempImageName = $this->createTemporaryImage($uploadedImage);
        $this->deleteOldImage($entityId);

        $destination = _PS_GENDERS_DIR_ . $entityId . '.jpg';
        $this->uploadFromTemp($tempImageName, $destination);

        // Copy new image
        if (!ImageManager::resize(
            $destination,
            $destination,
            $imageWidth,
            $imageHeight
        )) {
            throw new TitleImageUploadingException(
                'An error occurred while uploading the image. Check your directory permissions.',
                TitleImageUploadingException::UNEXPECTED_ERROR
            );
        }
    }

    /**
     * Deletes old image
     *
     * @param int $id
     */
    protected function deleteOldImage(int $id): void
    {
        $title = new Gender($id);
        $title->deleteImage();

        // Remove thumbnail
        if (!file_exists(_PS_GENDERS_DIR_ . $id . '.jpg')) {
            $currentFile = _PS_TMP_IMG_DIR_ . 'genders_mini_' . $id . '.jpg';

            if (file_exists($currentFile)) {
                unlink($currentFile);
            }
        }
    }
}
