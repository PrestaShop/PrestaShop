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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter\Image\Uploader;

use ErrorException;
use Hook;
use Image;
use ImageManager;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageUpdateException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShop\PrestaShop\Core\Image\Uploader\ImageUploaderInterface;
use PrestaShopException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

final class ProductImageUploader extends AbstractImageUploader implements ImageUploaderInterface
{
    /**
     * @var array
     */
    private $contextShopIdsList;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @var bool
     */
    private $isLegacyImageMode;

    /**
     * @param array $contextShopIdsList
     * @param int $contextShopId
     * @param bool $isLegacyImageMode
     */
    public function __construct(
        array $contextShopIdsList,
        int $contextShopId,
        bool $isLegacyImageMode
    ) {
        $this->contextShopIdsList = $contextShopIdsList;
        $this->contextShopId = $contextShopId;
        $this->isLegacyImageMode = $isLegacyImageMode;
    }

    /**
     * {@inheritDoc}
     */
    public function upload($productId, UploadedFile $uploadedImage, ?ImageId $imageId = null)
    {
        if (!$imageId) {
            throw new ImageUploadException('Image id is required to create path for product image');
        }

        $this->checkImageIsAllowedForUpload($uploadedImage);

        $temporaryImageName = $this->moveToTemporaryDir($uploadedImage);
        $image = $this->loadImageEntity($imageId);

        $this->checkMemory($temporaryImageName);
        $this->createDestinationDirectory($image);
        $this->copyToDestination($temporaryImageName, $image);
        //@todo: abstract method seems to not fit for product. check AdminProductsController:2865
        $this->generateDifferentSize(
            $productId,
            _PS_PROD_IMG_DIR_,
            'products'
        );

        Hook::exec('actionWatermark', ['id_image' => $image->id, 'id_product' => $productId]);
        $this->updateCover($image);
        $image->associateTo($this->contextShopIdsList);

        try {
            //@todo: this line was originally executed before the hook 'actionWatermark'. does it matter? AdminProductsController::2881
            unlink($temporaryImageName);

            unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $productId . '.jpg');
            unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $productId . '_' . $this->contextShopId . '.jpg');
        } catch (ErrorException $e) {
            //@todo in controller when catching this exception use a warning instead of error as in AttachmentController ?
            throw new CannotUnlinkImageException($e->getMessage());
        }

    }

    /**
     * @param string $tmpImageName
     * @param Image $image
     *
     * @throws ImageOptimizationException
     */
    private function copyToDestination(string $tmpImageName, Image $image)
    {
        if (!ImageManager::resize($tmpImageName, $this->getDestinationPath($image, true))) {
            throw new ImageOptimizationException('An error occurred while uploading the image. Check your directory permissions.');
        }
    }

    /**
     * Evaluate the memory required to resize the image: if it's too much, you can't resize it.
     *
     * @param string $tmpImageName
     *
     * @throws MemoryLimitException
     */
    private function checkMemory(string $tmpImageName): void
    {
        if (!ImageManager::checkImageMemoryLimit($tmpImageName)) {
            throw new MemoryLimitException('Due to memory limit restrictions, this image cannot be loaded. Increase your memory_limit value.');
        }
    }

    /**
     * @param UploadedFile $uploadedImage
     *
     * @return string temporary image name
     *
     * @throws ImageUploadException
     */
    private function moveToTemporaryDir(UploadedFile $uploadedImage): string
    {
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

        if (!$temporaryImageName) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        if (!move_uploaded_file($uploadedImage->getPathname(), $temporaryImageName)) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        return $temporaryImageName;
    }

    /**
     * @param Image $image\
     * @todo: check if this is really necessary
     */
    private function updateCover(Image $image): void
    {
        try {
            if (!$image->update()) {
                throw new ImageUpdateException(sprintf(
                    'Error occurred when updating image #%s cover',
                    $image->id
                ));
            }
        } catch (PrestaShopException $e) {
            throw new ImageException(sprintf('Error occurred when updating image #%s cover', $image->id),
                0,
                $e
            );
        }

    }

    /**
     * @param ImageId $imageId
     *
     * @return Image
     *
     * @throws ImageNotFoundException
     */
    private function loadImageEntity(ImageId $imageId): Image
    {
        $imageIdValue = $imageId->getValue();
        $image = new Image($imageIdValue);

        if ((int) $image->id !== $imageIdValue) {
            throw new ImageNotFoundException(sprintf(
                'Image entity with id #%s does not exist',
                $imageIdValue
            ));
        }

        return $image;
    }

    /**
     * @param Image $image
     *
     * @throws ImageUploadException
     */
    private function createDestinationDirectory(Image $image): void
    {
        if ($this->isLegacyImageMode || $image->createImgFolder()) {
            return;
        }

        throw new ImageUploadException(sprintf(
            'Error occurred when trying to create directory for product #%s image',
            $image->id_product
        ));
    }

    /**
     * @param Image $image
     * @param bool $withExtension
     *
     * @return string
     */
    private function getDestinationPath(Image $image, bool $withExtension): string
    {
        if ($this->isLegacyImageMode) {
            $path = $image->id_product . '-' . $image->id;
        } else {
            $path = $image->getImgPath();
        }

        //@todo: it seems that jpg is hardcoded. AdminProductsController:2836
        if ($withExtension) {
            $path .= sprintf('.%s', $image->image_format);
        }

        return _PS_PROD_IMG_DIR_ . $path;
    }
}
