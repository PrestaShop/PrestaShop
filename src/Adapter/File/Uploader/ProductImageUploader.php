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

namespace PrestaShop\PrestaShop\Adapter\File\Uploader;

use ErrorException;
use Hook;
use Image;
use ImageManager;
use PrestaShop\PrestaShop\Adapter\Image\Uploader\AbstractImageUploader;
use PrestaShop\PrestaShop\Core\Configuration\UploadSizeConfigurationInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\CannotUnlinkImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageConstraintException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageNotFoundException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\Exception\ImageUpdateException;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ImagePathFactoryInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ProductImageUploaderInterface;
use PrestaShop\PrestaShop\Core\Domain\Product\Image\ValueObject\ImageId;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageOptimizationException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\ImageUploadException;
use PrestaShop\PrestaShop\Core\Image\Uploader\Exception\MemoryLimitException;
use PrestaShopException;
use Symfony\Component\Mime\MimeTypes;

final class ProductImageUploader extends AbstractImageUploader implements ProductImageUploaderInterface
{
    /**
     * @var UploadSizeConfigurationInterface
     */
    private $uploadSizeConfiguration;

    /**
     * @var ImagePathFactoryInterface
     */
    private $productImagePathFactory;

    /**
     * @var array
     */
    private $contextShopIdsList;

    /**
     * @var int
     */
    private $contextShopId;

    /**
     * @param UploadSizeConfigurationInterface $uploadSizeConfiguration
     * @param ImagePathFactoryInterface $productImagePathFactory
     * @param array $contextShopIdsList
     * @param int $contextShopId
     */
    public function __construct(
        UploadSizeConfigurationInterface $uploadSizeConfiguration,
        ImagePathFactoryInterface $productImagePathFactory,
        array $contextShopIdsList,
        int $contextShopId
    ) {
        $this->uploadSizeConfiguration = $uploadSizeConfiguration;
        $this->productImagePathFactory = $productImagePathFactory;
        $this->contextShopIdsList = $contextShopIdsList;
        $this->contextShopId = $contextShopId;
    }

    /**
     * {@inheritdoc}
     */
    public function upload(
        ImageId $imageId,
        string $filePath,
        int $fileSize,
        string $format
    ): void {
        $this->checkSize($fileSize);
        $tmpImageName = $this->moveToTemporaryDir($filePath);
        $image = $this->loadImageEntity($imageId);

        $this->checkMemory($tmpImageName);
        $this->productImagePathFactory->createDestinationDirectory($imageId);
        $this->copyToDestination($tmpImageName, $imageId);

        $this->generateDifferentSizeImages(
            $this->productImagePathFactory->getBasePath($imageId, false),
            'products',
            $format
        );

        Hook::exec('actionWatermark', ['id_image' => $image->id, 'id_product' => $image->id_product]);
        //@Todo: wait for multishop specs
        $image->associateTo($this->contextShopIdsList);

        try {
            //@todo: this line was originally executed before the hook 'actionWatermark'. does it matter? AdminProductsController::2881
            unlink($tmpImageName);

            unlink(_PS_TMP_IMG_DIR_ . 'product_' . (int) $image->id. '.jpg');
            unlink(_PS_TMP_IMG_DIR_ . 'product_mini_' . (int) $image->id_product . '_' . $this->contextShopId . '.jpg');
        } catch (ErrorException $e) {
            //@todo in controller when catching this exception use a warning instead of error as in AttachmentController ?
            throw new CannotUnlinkImageException($e->getMessage());
        }
    }

    /**
     * @param string $filePath
     *
     * @return string temporary image name
     *
     * @throws ImageUploadException
     */
    private function moveToTemporaryDir(string $filePath): string
    {
        $temporaryImageName = tempnam(_PS_TMP_IMG_DIR_, 'PS');

        if (!$temporaryImageName) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        if (!move_uploaded_file($filePath, $temporaryImageName)) {
            throw new ImageUploadException('An error occurred while uploading the image. Check your directory permissions.');
        }

        return $temporaryImageName;
    }

    /**
     * @param string $tmpImageName
     * @param ImageId $imageId
     *
     * @throws ImageOptimizationException
     */
    private function copyToDestination(string $tmpImageName, ImageId $imageId)
    {
        if (!ImageManager::resize($tmpImageName, $this->productImagePathFactory->getBasePath($imageId, true))) {
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
     * @param int $fileSize
     *
     * @throws ImageConstraintException
     */
    private function checkSize(int $fileSize): void
    {
        $maxFileSize = $this->uploadSizeConfiguration->getMaxUploadSizeInBytes();

        if ($maxFileSize > 0 && $fileSize > $maxFileSize) {
            throw new ImageConstraintException(
                sprintf('Max file size allowed is "%s" bytes. Uploaded file size is "%s".', $maxFileSize, $fileSize),
                ImageConstraintException::INVALID_FILE_SIZE
            );
        }
    }
}
